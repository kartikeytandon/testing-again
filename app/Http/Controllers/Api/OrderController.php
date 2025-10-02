<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('client');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_details', 'like', "%{$search}%")
                  ->orWhere('remark', 'like', "%{$search}%");
            });
        }

        // Role-based filtering
        $user = $request->user();
        if ($user->isClient()) {
            $query->where('client_id', $user->id);
        }

        $perPage = $request->get('per_page', 15);
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:users,id',
            'order_details' => 'required|string',
            'remark' => 'nullable|string',
            'status' => 'nullable|in:pending,ordered_with_supplier,billing_dispatch,delivered',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        // For client users, automatically set client_id to their own ID
        if ($user->isClient()) {
            $request->merge(['client_id' => $user->id]);
        }

        $order = Order::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order->load('client')
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $order = Order::with('client')->findOrFail($id);

        // Check if user can view this order
        $user = $request->user();
        if ($user->isClient() && $order->client_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Check if user can update this order
        $user = $request->user();
        if ($user->isClient() && $order->client_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'order_details' => 'sometimes|required|string',
            'remark' => 'nullable|string',
            'status' => 'sometimes|in:pending,ordered_with_supplier,billing_dispatch,delivered',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $order->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => $order->load('client')
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Only admin can delete orders
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,ordered_with_supplier,billing_dispatch,delivered',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order->load('client')
        ]);
    }

    public function getByStatus(Request $request, $status)
    {
        $query = Order::with('client')->where('status', $status);

        // Role-based filtering
        $user = $request->user();
        if ($user->isClient()) {
            $query->where('client_id', $user->id);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function getByClient(Request $request, $clientId)
    {
        $query = Order::with('client')->where('client_id', $clientId);

        // Role-based filtering
        $user = $request->user();
        if ($user->isClient() && $clientId != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Order::with('client');
        $search = $request->q;

        $query->where(function ($q) use ($search) {
            $q->where('order_details', 'like', "%{$search}%")
              ->orWhere('remark', 'like', "%{$search}%");
        });

        // Role-based filtering
        $user = $request->user();
        if ($user->isClient()) {
            $query->where('client_id', $user->id);
        }

        $perPage = $request->get('per_page', 15);
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function getStatuses()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'pending' => 'Pending',
                'ordered_with_supplier' => 'Ordered with Supplier',
                'billing_dispatch' => 'Billing & Dispatch',
                'delivered' => 'Delivered'
            ]
        ]);
    }
}
