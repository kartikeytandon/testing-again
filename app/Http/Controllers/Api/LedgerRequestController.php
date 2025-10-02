<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LedgerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LedgerRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = LedgerRequest::with('client');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('date_from')) {
            $query->where('request_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('request_date', '<=', $request->date_to);
        }

        // Role-based filtering
        if ($request->user()->isClient()) {
            $query->where('client_id', $request->user()->id);
        }

        $perPage = $request->get('per_page', 15);
        $requests = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ledgerRequest = LedgerRequest::create([
            'client_id' => $request->user()->id,
            'request_date' => $request->request_date,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ledger request created successfully',
            'data' => $ledgerRequest
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $ledgerRequest = LedgerRequest::with('client')->findOrFail($id);

        // Role-based access control
        if ($request->user()->isClient() && $ledgerRequest->client_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $ledgerRequest
        ]);
    }

    public function update(Request $request, $id)
    {
        $ledgerRequest = LedgerRequest::findOrFail($id);

        // Only clients can update their own requests
        if ($request->user()->isClient() && $ledgerRequest->client_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'request_date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ledgerRequest->update($request->only(['request_date']));

        return response()->json([
            'success' => true,
            'message' => 'Ledger request updated successfully',
            'data' => $ledgerRequest
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $ledgerRequest = LedgerRequest::findOrFail($id);

        // Only clients can delete their own requests
        if ($request->user()->isClient() && $ledgerRequest->client_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $ledgerRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ledger request deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        // Only admin and office staff can update status
        if (!$request->user()->isAdmin() && !$request->user()->isOfficeStaff()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,uploaded,confirmed,rejected',
            'admin_remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ledgerRequest = LedgerRequest::findOrFail($id);
        $ledgerRequest->update([
            'status' => $request->status,
            'admin_remarks' => $request->admin_remarks,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => $ledgerRequest
        ]);
    }

    public function getByStatus(Request $request, $status)
    {
        $query = LedgerRequest::with('client')->where('status', $status);

        // Role-based filtering
        if ($request->user()->isClient()) {
            $query->where('client_id', $request->user()->id);
        }

        $perPage = $request->get('per_page', 15);
        $requests = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    public function adminIndex(Request $request)
    {
        // Only admin and office staff can access admin index
        if (!$request->user()->isAdmin() && !$request->user()->isOfficeStaff()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $query = LedgerRequest::with('client');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $perPage = $request->get('per_page', 15);
        $requests = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    public function uploadFile(Request $request, $id)
    {
        // Only admin and office staff can upload files
        if (!$request->user()->isAdmin() && !$request->user()->isOfficeStaff()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ledgerRequest = LedgerRequest::findOrFail($id);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('ledger_files', $filename, 'public');

            $ledgerRequest->update([
                'ledger_file_path' => $path,
                'status' => 'uploaded',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'data' => $ledgerRequest
        ]);
    }

    public function confirm(Request $request, $id)
    {
        // Only admin can confirm ledger requests
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $ledgerRequest = LedgerRequest::findOrFail($id);
        $ledgerRequest->update(['status' => 'confirmed']);

        return response()->json([
            'success' => true,
            'message' => 'Ledger request confirmed successfully',
            'data' => $ledgerRequest
        ]);
    }

    public function reject(Request $request, $id)
    {
        // Only admin can reject ledger requests
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'admin_remarks' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ledgerRequest = LedgerRequest::findOrFail($id);
        $ledgerRequest->update([
            'status' => 'rejected',
            'admin_remarks' => $request->admin_remarks,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ledger request rejected successfully',
            'data' => $ledgerRequest
        ]);
    }

    public function download(Request $request, $id)
    {
        $ledgerRequest = LedgerRequest::findOrFail($id);

        // Role-based access control
        if ($request->user()->isClient() && $ledgerRequest->client_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        if (!$ledgerRequest->ledger_file_path) {
            return response()->json([
                'success' => false,
                'message' => 'No file available for download'
            ], 404);
        }

        if (!Storage::disk('public')->exists($ledgerRequest->ledger_file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        return Storage::disk('public')->download($ledgerRequest->ledger_file_path);
    }

    public function uploadLedger(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB max
            'request_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ledgerRequest = LedgerRequest::create([
            'client_id' => $request->user()->id,
            'request_date' => $request->request_date,
            'status' => 'pending',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('ledger_files', $filename, 'public');

            $ledgerRequest->update([
                'ledger_file_path' => $path,
                'status' => 'uploaded',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ledger uploaded successfully',
            'data' => $ledgerRequest
        ], 201);
    }
}
