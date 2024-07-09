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

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="API Documentation",
 *         version="1.0.0",
 *         description="This is the API documentation for your project.",
 *         @OA\Contact(
 *             email="vicmwe184@gmail.com"
 *         ),
 *         @OA\License(
 *             name="Apache 2.0",
 *             url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *         )
 *     )
 * )
 */

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
Route::middleware(['auth:sanctum','throttle:1000,1'])->get('/list-users', [AuthController::class, 'index']);
Route::middleware('auth:sanctum')->get('/list-user-by-branch/{branchId}', [AuthController::class, 'getUserByBranch']);
Route::middleware('auth:sanctum')->get('/active-user', [AuthController::class, 'get_current_logged_in_user']);
Route::middleware('auth:sanctum')->get('/user-data', [AuthController::class, 'get_user_data']);
Route::middleware('auth:sanctum')->delete('/delete-user/{id}', [AuthController::class, 'destroy']);
Route::middleware('auth:sanctum')->put('/edit-user/{id}', [AuthController::class, 'editUser']);

/**
 * CRUD Operations on a Firm
 */
Route::middleware(['auth:sanctum', 'throttle:10000,1'])->get('/list-firms', [\App\Http\Controllers\FirmController::class, 'index']);
Route::middleware('auth:sanctum')->get('/firm-count', [\App\Http\Controllers\FirmController::class, 'getFirmCount']);
Route::middleware('auth:sanctum')->get('/list-firms/{id}', [\App\Http\Controllers\FirmController::class, 'show']);
// get branches belonging to a firm
Route::middleware('auth:sanctum')->get('/list-firms/{id}/branches', [\App\Http\Controllers\FirmController::class, 'getAssociatedBranches']);
Route::middleware('auth:sanctum')->post('/register-firm', [\App\Http\Controllers\FirmController::class, 'store']);
Route::middleware('auth:sanctum')->put('/update-firm/{id}', [\App\Http\Controllers\FirmController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/delete-firm/{id}', [\App\Http\Controllers\FirmController::class, 'destroy']);
Route::middleware('auth:sanctum')->get('/export-firm-data/{id}', [\App\Http\Controllers\FirmController::class, 'exportFirmData'])->name('export.firm.data');
Route::middleware('auth:sanctum')->get('/export-firm-data/{id}', [\App\Http\Controllers\ExportController::class, 'exportFirmData'])->name('export.firm.data');
//Route::get('/export-firm-data/{id}', [\App\Http\Controllers\FirmController::class, 'exportFirmData']);


/**
 * LEAD DETAILS
 */
Route::middleware('auth:sanctum')->get('/list-leads', [\App\Http\Controllers\LeadController::class, 'index']);
Route::middleware('auth:sanctum')->get('/lead-count', [\App\Http\Controllers\LeadController::class, 'getLeadData']);


/**
 * CRUD Operations on a Branch
 */
Route::middleware('auth:sanctum')->post('/register-branch', [\App\Http\Controllers\BranchController::class, 'store']);
Route::middleware('auth:sanctum')->get('/list-branches', [\App\Http\Controllers\BranchController::class, 'index']);
Route::middleware('auth:sanctum')->get('/list-branches/{id}', [\App\Http\Controllers\BranchController::class, 'show']);
Route::middleware('auth:sanctum')->delete('/delete-branch/{id}', [\App\Http\Controllers\BranchController::class, 'destroy']);
Route::middleware('auth:sanctum')->get('/store-data', [StoreController::class, 'get_store_data']);


/**
 * CRUD Operations on a Move
 */
Route::middleware('auth:sanctum')->post('/register-move', [MoveController::class, 'create_move']);
Route::middleware('auth:sanctum')->get('/list-moves', [MoveController::class, 'index']);
Route::middleware('auth:sanctum')->get('/list-moves-show/{id}', [MoveController::class, 'show']);
Route::middleware('auth:sanctum')->put('/update-move/{id}', [MoveController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/delete-move/{id}', [MoveController::class, 'destroy']);
Route::middleware('auth:sanctum')->get('/move-data', [MoveController::class, 'get_move_data']);
Route::middleware('auth:sanctum')->post('/moves-per-month/{year}', [MoveController::class, 'get_moves_per_month']);


/*
 * Invoice CRUD and mail
 */
Route::middleware('auth:sanctum')->post('/create-invoice', [\App\Http\Controllers\InvoiceController::class, 'store']);
Route::middleware('auth:sanctum')->get('/list-invoice', [\App\Http\Controllers\InvoiceController::class, 'index']);
Route::middleware('auth:sanctum')->post('/send-invoice/{id}', [\App\Http\Controllers\InvoiceController::class, 'send_invoice']);


/*
 * Email Setup
 */
Route::middleware('auth:sanctum')->post('/setup-email', [\App\Http\Controllers\EmailSetupController::class, 'store']);
Route::middleware('auth:sanctum')->get('/list-email-setup', [\App\Http\Controllers\EmailSetupController::class, 'index']);
Route::middleware('auth:sanctum')->delete('/setup-email/delete/{id}', [\App\Http\Controllers\EmailSetupController::class, 'destroy']);

// COMPOSER HOME DIR
/**
 * /home/kejadigital/www/test.kejadigital.com/composer
*/

//Route::get('swagger', function () {
//    return view('swagger');
//});

