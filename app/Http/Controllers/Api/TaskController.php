<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('user');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('date')) {
            $query->whereDate('task_date', $request->date);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Role-based filtering
        $user = $request->user();
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $perPage = $request->get('per_page', 15);
        $tasks = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_title' => 'required|string|max:255',
            'task_description' => 'required|string',
            'task_date' => 'required|date',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date|after_or_equal:task_date',
            'remarks' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        // For non-admin users, they can only create tasks for themselves
        if (!$user->isAdmin() && $request->user_id != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only create tasks for yourself'
            ], 403);
        }

        $task = Task::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task->load('user')
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $task = Task::with('user')->findOrFail($id);

        // Check if user can view this task
        $user = $request->user();
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Check if user can update this task
        $user = $request->user();
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'task_title' => 'sometimes|required|string|max:255',
            'task_description' => 'sometimes|required|string',
            'task_date' => 'sometimes|required|date',
            'priority' => 'sometimes|required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date|after_or_equal:task_date',
            'remarks' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,done,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $task->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $task->load('user')
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Check if user can delete this task
        $user = $request->user();
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,done,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $task->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully',
            'data' => $task->load('user')
        ]);
    }

    public function complete(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $task->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task marked as completed',
            'data' => $task->load('user')
        ]);
    }

    public function getByStatus(Request $request, $status)
    {
        $query = Task::with('user')->where('status', $status);

        // Role-based filtering
        $user = $request->user();
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    public function getByPriority(Request $request, $priority)
    {
        $query = Task::with('user')->where('priority', $priority);

        // Role-based filtering
        $user = $request->user();
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    public function getOverdue(Request $request)
    {
        $query = Task::with('user')
            ->where('status', '!=', 'completed')
            ->where('due_date', '<', now()->toDateString());

        // Role-based filtering
        $user = $request->user();
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $tasks = $query->orderBy('due_date', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    public function getByDate(Request $request, $date)
    {
        $query = Task::with('user')->whereDate('task_date', $date);

        // Role-based filtering
        $user = $request->user();
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    public function getByUser(Request $request, $userId)
    {
        // Only admin can view tasks by user
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $tasks = Task::with('user')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    public function assign(Request $request)
    {
        // Only admin can assign tasks
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'task_title' => 'required|string|max:255',
            'task_description' => 'required|string',
            'task_date' => 'required|date',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date|after_or_equal:task_date',
            'remarks' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $task = Task::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Task assigned successfully',
            'data' => $task->load('user')
        ], 201);
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

        $query = Task::with('user');
        $search = $request->q;

        $query->where(function ($q) use ($search) {
            $q->where('task_title', 'like', "%{$search}%")
              ->orWhere('task_description', 'like', "%{$search}%")
              ->orWhere('remarks', 'like', "%{$search}%");
        });

        // Role-based filtering
        $user = $request->user();
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $perPage = $request->get('per_page', 15);
        $tasks = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }
}
