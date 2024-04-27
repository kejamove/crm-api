<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\StoreController;
use App\Models\User;

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


Route::resource('products', ProductController::class);
Route::get('products/search/{product_name}', [ProductController::class, 'search']);
Route::post('/email', [EmailController::class, 'send_email']);

// Basic Auth
Route::middleware('auth:sanctum')->post('/register-user', [AuthController::class, 'register_user']);

Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/login', [AuthController::class, 'login']);

// Get system wide users
Route::middleware('auth:sanctum')->get('/all-users', [AuthController::class, 'get_all_users']);

// Get the current logged in user
Route::middleware('auth:sanctum')->get('/active-user', [AuthController::class, 'get_current_logged_in_user']);


// Store
Route::middleware('auth:sanctum')->post('/register-store', [StoreController::class, 'store']);
Route::middleware('auth:sanctum')->get('/all-stores', [StoreController::class, 'index']);
