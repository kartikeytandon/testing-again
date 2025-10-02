<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoutingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root and default login to CRM login
Route::get('/', fn() => redirect('/crm-login'))->name('root');
Route::get('/login', fn() => redirect('/crm-login'))->name('login');

// Load Laravel's default auth routes after our overrides
require __DIR__ . '/auth.php';

// CRM Routes (no middleware - handled by frontend)
Route::get('/crm-login', fn() => view('auth.crm-login'))->name('crm.login');
Route::get('/crm', fn() => view('crm.dashboard'))->name('crm.dashboard');
Route::get('/admin', fn() => view('admin.panel'))->name('admin.panel');

Route::group(['prefix' => '/', 'middleware' => 'auth'], function () {
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any')->where('any', '^(?!api).*');
});

