<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\MoveController;
use App\Http\Controllers\LeadController;
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

/**
 * Email Notifications
 */
Route::post('/possible-move-email-notification', [EmailController::class, 'email_received_and_under_review']);
Route::post('/successful-move-email-notification', [EmailController::class, 'close_move_and_give_client_feedback']);

/**
 * Authenticate Users
 */
Route::middleware('auth:sanctum')->post('/register-user', [AuthController::class, 'register_user']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/login', [AuthController::class, 'login']);

/**
 * List User(s) details
 */
Route::middleware('auth:sanctum')->get('/all-users', [AuthController::class, 'get_all_users']);
Route::middleware('auth:sanctum')->get('/active-user', [AuthController::class, 'get_current_logged_in_user']);


/**
 * CRUD Operations on a Store
 */
Route::middleware('auth:sanctum')->post('/register-store', [StoreController::class, 'store']);
Route::middleware('auth:sanctum')->get('/all-stores', [StoreController::class, 'index']);


/**
 * Move
 */
Route::middleware('auth:sanctum')->post('/register-move', [MoveController::class, 'create_move']);  
Route::middleware('auth:sanctum')->get('/all-moves', [MoveController::class, 'index']);
Route::middleware('auth:sanctum')->get('/all-moves/{id}', [MoveController::class, 'show']);

/**
 * Lead
 */
Route::middleware('auth:sanctum')->post('/register-lead', [LeadController::class, 'create_lead']);  
Route::middleware('auth:sanctum')->get('/all-leads', [LeadController::class, 'index']);

