<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\LedgerRequestController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\SalesReportController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

Route::get('/debug', function () {
    return response()->json(['message' => 'API routes are loading']);
});


Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

    // User management routes
    // Admin-only: create users
    Route::post('/users', [UserController::class, 'store'])->middleware('is_admin');
    // Other user routes
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::get('/users/roles', [UserController::class, 'getRoles']);
    Route::put('/users/{id}/role', [UserController::class, 'updateRole']);
    Route::put('/users/{id}/modules', [UserController::class, 'updateModules']);

    // Order management routes
    Route::apiResource('orders', OrderController::class);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::get('/orders/status/{status}', [OrderController::class, 'getByStatus']);
    Route::get('/orders/client/{clientId}', [OrderController::class, 'getByClient']);
    Route::get('/orders/search', [OrderController::class, 'search']);
    Route::get('/orders/statuses', [OrderController::class, 'getStatuses']);

    // Ledger request routes
    Route::apiResource('ledger-requests', LedgerRequestController::class);
    Route::put('/ledger-requests/{id}/status', [LedgerRequestController::class, 'updateStatus']);
    Route::get('/ledger-requests/status/{status}', [LedgerRequestController::class, 'getByStatus']);

    // Admin ledger management routes
    Route::prefix('admin')->group(function () {
        Route::get('/ledger-requests', [LedgerRequestController::class, 'adminIndex']);
        Route::post('/ledger-requests/{id}/upload', [LedgerRequestController::class, 'uploadFile']);
        Route::put('/ledger-requests/{id}/confirm', [LedgerRequestController::class, 'confirm']);
        Route::put('/ledger-requests/{id}/reject', [LedgerRequestController::class, 'reject']);
        Route::get('/ledger-requests/{id}/download', [LedgerRequestController::class, 'download']);
    });

    // Task management routes
    Route::apiResource('tasks', TaskController::class);
    Route::put('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
    Route::put('/tasks/{id}/complete', [TaskController::class, 'complete']);
    Route::get('/tasks/status/{status}', [TaskController::class, 'getByStatus']);
    Route::get('/tasks/priority/{priority}', [TaskController::class, 'getByPriority']);
    Route::get('/tasks/overdue', [TaskController::class, 'getOverdue']);
    Route::get('/tasks/date/{date}', [TaskController::class, 'getByDate']);
    Route::get('/tasks/user/{userId}', [TaskController::class, 'getByUser']);
    Route::post('/tasks/assign', [TaskController::class, 'assign']);

    // Attendance routes
    Route::apiResource('attendance', AttendanceController::class);
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendance/today', [AttendanceController::class, 'getToday']);
    Route::get('/attendance/date/{date}', [AttendanceController::class, 'getByDate']);
    Route::get('/attendance/range', [AttendanceController::class, 'getByRange']);
    Route::get('/attendance/status/{status}', [AttendanceController::class, 'getByStatus']);
    Route::get('/attendance/user/{userId}', [AttendanceController::class, 'getByUser']);
    Route::get('/attendance/report', [AttendanceController::class, 'getReport']);
    Route::get('/attendance/summary', [AttendanceController::class, 'getSummary']);

    // Sales reporting routes
    Route::apiResource('sales-reports', SalesReportController::class);
    Route::post('/sales-reports/{id}/photo', [SalesReportController::class, 'uploadPhoto']);
    Route::get('/sales-reports/{id}/photo', [SalesReportController::class, 'getPhoto']);
    Route::get('/sales-reports/date/{date}', [SalesReportController::class, 'getByDate']);
    Route::get('/sales-reports/client/{clientId}', [SalesReportController::class, 'getByClient']);
    Route::get('/sales-reports/salesperson/{userId}', [SalesReportController::class, 'getBySalesperson']);
    Route::get('/sales-reports/analytics', [SalesReportController::class, 'getAnalytics']);
    Route::get('/sales-reports/summary', [SalesReportController::class, 'getSummary']);
    Route::get('/sales-reports/top-clients', [SalesReportController::class, 'getTopClients']);
    Route::get('/sales-reports/performance', [SalesReportController::class, 'getPerformance']);

    // Module access routes
    Route::get('/modules', [UserController::class, 'getModules']);
    Route::get('/modules/permissions', [UserController::class, 'getPermissions']);
    Route::get('/modules/{userId}/access', [UserController::class, 'getUserModuleAccess']);
    Route::put('/modules/{userId}/access', [UserController::class, 'updateUserModuleAccess']);
    Route::post('/modules/assign', [UserController::class, 'assignModule']);
    Route::delete('/modules/revoke', [UserController::class, 'revokeModule']);

    // Dashboard routes
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/overview', [DashboardController::class, 'getOverview']);
    Route::get('/dashboard/recent-activity', [DashboardController::class, 'getRecentActivity']);

    // Search routes
    Route::get('/search/orders', [OrderController::class, 'search']);
    Route::get('/search/users', [UserController::class, 'search']);
    Route::get('/search/clients', [UserController::class, 'searchClients']);
    Route::get('/search/tasks', [TaskController::class, 'search']);

    // File upload routes
    Route::post('/upload/ledger', [LedgerRequestController::class, 'uploadLedger']);
    Route::post('/upload/visit-photo', [SalesReportController::class, 'uploadVisitPhoto']);
    Route::get('/upload/files/{file}', [UserController::class, 'downloadFile']);
    Route::delete('/upload/files/{file}', [UserController::class, 'deleteFile']);
});
