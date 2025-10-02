<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ModuleAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Only admin can list all users
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $query = User::with('moduleAccess');

        // Apply filters
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        // Only admin can create users
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,sales_person,office_staff,client',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $user = User::with('moduleAccess')->findOrFail($id);
        
        // Users can only view their own profile unless they're admin
        if (!$request->user()->isAdmin() && $request->user()->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Users can only update their own profile unless they're admin
        if (!$request->user()->isAdmin() && $request->user()->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only(['name', 'email', 'phone', 'address']));

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    public function destroy(Request $request, $id)
    {
        // Only admin can delete users
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    public function getRoles()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'admin' => 'Administrator',
                'sales_person' => 'Sales Person',
                'office_staff' => 'Office Staff',
                'client' => 'Client'
            ]
        ]);
    }

    public function updateRole(Request $request, $id)
    {
        // Only admin can update roles
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|string|in:admin,sales_person,office_staff,client',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($id);
        $user->update(['role' => $request->role]);

        return response()->json([
            'success' => true,
            'message' => 'User role updated successfully',
            'data' => $user
        ]);
    }

    public function updateModules(Request $request, $id)
    {
        // Only admin can update module access
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'modules' => 'required|array',
            'modules.*.module_name' => 'required|string',
            'modules.*.permissions' => 'required|array',
            'modules.*.is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($id);
        
        // Delete existing module access
        $user->moduleAccess()->delete();
        
        // Create new module access
        foreach ($request->modules as $module) {
            ModuleAccess::create([
                'user_id' => $user->id,
                'module_name' => $module['module_name'],
                'permissions' => $module['permissions'],
                'is_active' => $module['is_active'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User module access updated successfully',
            'data' => $user->load('moduleAccess')
        ]);
    }

    public function getModules()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'orders' => 'Order Management',
                'ledger_requests' => 'Ledger Requests',
                'tasks' => 'Task Management',
                'attendance' => 'Attendance Management',
                'sales_reports' => 'Sales Reports',
                'user_management' => 'User Management',
                'dashboard' => 'Dashboard'
            ]
        ]);
    }

    public function getPermissions()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'approve' => 'Approve',
                'export' => 'Export'
            ]
        ]);
    }

    public function getUserModuleAccess($userId)
    {
        $user = User::with('moduleAccess')->findOrFail($userId);
        
        return response()->json([
            'success' => true,
            'data' => $user->moduleAccess
        ]);
    }

    public function updateUserModuleAccess(Request $request, $userId)
    {
        // Only admin can update module access
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'module_name' => 'required|string',
            'permissions' => 'required|array',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($userId);
        
        ModuleAccess::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_name' => $request->module_name,
            ],
            [
                'permissions' => $request->permissions,
                'is_active' => $request->is_active,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Module access updated successfully',
            'data' => $user->load('moduleAccess')
        ]);
    }

    public function assignModule(Request $request)
    {
        // Only admin can assign modules
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'module_name' => 'required|string',
            'permissions' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        ModuleAccess::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'module_name' => $request->module_name,
            ],
            [
                'permissions' => $request->permissions,
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Module assigned successfully'
        ]);
    }

    public function revokeModule(Request $request)
    {
        // Only admin can revoke modules
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'module_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        ModuleAccess::where('user_id', $request->user_id)
            ->where('module_name', $request->module_name)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Module revoked successfully'
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function searchClients(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        $clients = User::where('role', 'client')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $clients
        ]);
    }

    public function downloadFile($file)
    {
        // Implementation for file download
        return response()->json([
            'success' => false,
            'message' => 'File download not implemented yet'
        ], 501);
    }

    public function deleteFile($file)
    {
        // Implementation for file deletion
        return response()->json([
            'success' => false,
            'message' => 'File deletion not implemented yet'
        ], 501);
    }
}
