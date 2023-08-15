<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Admin\AppointmentController;

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

Route::get('/api/users', [UserController::class, 'index']);
Route::post('/api/users', [UserController::class, 'store']);
Route::put('/api/users/{user}', [UserController::class, 'update']);
Route::delete('/api/users/{user}', [UserController::class, 'destroy']);
Route::patch('/api/users/{user}/change-role', [UserController::class, 'changeRole']);
Route::get('/api/users/search', [UserController::class, 'search']);
Route::delete('/api/users', [UserController::class, 'bulkDelete']);


//Route::post('/api/appointments/create', [AppointmentController::class, 'store']);
Route::post('/api/appointments', [AppointmentController::class, 'store']);
Route::get('/api/appointments', [AppointmentController::class, 'index']);


Route::get('{view}', ApplicationController::class)->where('view', '(.*)');