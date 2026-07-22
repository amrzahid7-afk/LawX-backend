<?php

use App\Enums\RoleSlug;
use App\Http\Controllers\Api\LegislationController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\alertsController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\LegalRequestController;
use App\Http\Controllers\ManageOfficesController;
use App\Http\Controllers\ManageSyndicateUsers;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\UserManagmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', fn() => 'API Working');

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);
    // lawer register flow
    Route::post('/lawyer/verify-syndicate', [AuthController::class, 'verifySyndicate']);
    Route::post('/lawyer/register', [AuthController::class, 'register']);
    // client register flow
    Route::post('/client/register', [AuthController::class, 'registerClient']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/{id}/mark-read', [NotificationController::class, 'markAsRead']);
        Route::get('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    });
    // Api signature For DocuSign 
    // Route::post('/webhooks/docusign', [SignatureController::class, 'handleDocuSignWebhook']);
    // Route::get('/dashboard', [DashboardController::class, 'getStatistics']);
    // Route::get('/dashboard/alerts', [AlertsController::class, 'index']);
});

// Signature Apis 
Route::post('/docusign-webhook', [SignatureController::class, 'handleDocuSignWebhook']);
// Route::get('/files', [SignatureController::class, 'getLawyerFiles']);
// Route::get('/client/files', [SignatureController::class, 'getClientFiles']);
// Route::get('/syndicate/files', [SignatureController::class, 'getSyndicateFiles']);
// Route::get('/review-file/{signature_request_id}', [SignatureController::class, 'reviewFile']);

Route::middleware(['auth:sanctum', 'role:' . RoleSlug::Lawyer->value, 'platform'])
    ->prefix('lawyer')
    ->group(base_path('routes/lawyer.php'));

Route::middleware(['auth:sanctum', 'role:' . RoleSlug::Client->value, 'platform'])
    ->prefix('client')
    ->group(base_path('routes/client.php'));

Route::middleware(['auth:sanctum', 'role:' . RoleSlug::Syndicate->value, 'platform'])
    ->prefix('syndicate')
    ->group(base_path('routes/syndicate.php'));
