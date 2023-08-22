<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\DashboardstatsController;

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

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/admin/dashboard', function(){
//     return view('dashboard');
// }); /api/appointment

Route::middleware('auth')->group(function(){
    Route::get('/api/users', [UserController::class, 'index']);
    Route::post('/api/users', [UserController::class, 'store']);
    Route::put('/api/users/{user}', [UserController::class, 'update']);
    Route::delete('/api/users/{user}', [UserController::class, 'destroy']);
    Route::patch('/api/users/{user}/change-role', [UserController::class, 'changeRole']);
    Route::get('/api/users/search', [UserController::class, 'search']);
    Route::delete('/api/users', [UserController::class, 'bulkDelete']);
    Route::get('/api/client', [ClientController::class, 'index']);

   ///api/stats/users
    Route::get('/api/stats/users', [DashboardstatsController::class, 'users']);

    Route::get('/api/stats/dashboard', [DashboardstatsController::class, 'DashboardStats']);

    Route::get('/api/stats/appointments', [DashboardstatsController::class, 'appointments']);
    Route::post('/api/appointments', [AppointmentController::class, 'store']);
    Route::get('/api/appointments', [AppointmentController::class, 'index']);
    Route::get('/api/appointments/{appointment}/edit', [AppointmentController::class, 'edit']);
    Route::put('/api/appointments/{appointment}/edit', [AppointmentController::class, 'update']);
    Route::delete('/api/appointments/{appointment}', [AppointmentController::class, 'destroy']);

    //settings
    Route::get('/api/settings', [SettingsController ::class, 'settings']);
    Route::post('/api/settings', [SettingsController ::class, 'update']);

    Route::get('/api/profile', [ProfileController ::class, 'index']);
    Route::post('/api/profile', [ProfileController ::class, 'update']);
    Route::post('/api/upload-profile-image', [ProfileController ::class, 'uploadImage']);

    Route::post('/api/changePassword', [ProfileController ::class, 'changePassword']);

    

    

});







Route::get('{view}', ApplicationController::class)->where('view', '(.*)')->middleware('auth');