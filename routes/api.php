<?php

use App\Http\Controllers\Api\LegislationController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\Dashboard\alertsController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LegalRequestController;
use App\Http\Controllers\ManageOfficesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TaskController;
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

// Route::post('/tokens/create', function (Request $request) {
//     dd($token = $request->user()->createToken($request->token_name));
//     return ['token' => $token->plainTextToken];
//     return "asa";
// });


Route::get('/test', function () {
    return "API Working";
});
Route::prefix('/Auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);  //  
    Route::post('password/forgot', [AuthController::class, 'forgotPassword']); // 
    Route::post('password/reset', [AuthController::class, 'resetPassword']); // 
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});
Route::prefix('/Case')->middleware('auth:sanctum')->group(function () {
    // cases apis 
    Route::post('/create_case', [CaseController::class, 'create']); //  
    Route::post('/update_case',  [CaseController::class, 'update']);
    Route::get('/get_cases', [CaseController::class, 'view_list']);
    Route::get('/get_case_details/{case_id}', [CaseController::class, 'get_case_details']); // 
    Route::delete('/delete_case/{case_id}', [CaseController::class, 'delete_case']); //  
    // documents apis
    Route::post('/upload_document', [DocumentController::class, 'upload_Document']);   // 
    Route::get('/get_documents_for_case/{case_id}', [DocumentController::class, 'get_documents_for_case']);  // 
    Route::get('/delete_document/{document_id}', [DocumentController::class, 'delete_document']); // 
    Route::get('/document_versions/{document_id}', [DocumentController::class, 'delete_document']); // Not Done

    // Tasks apis  
    Route::post('/create_task', [TaskController::class, 'create_task']); // 
    Route::get('/get_my_tasks', [TaskController::class, 'get_my_tasks']); // to do 
    Route::get('/get_tasks_for_case/{case_id}', [TaskController::class, 'get_tasks_for_case']);
    Route::post('/edit_task', [TaskController::class, 'edit_task']); // 
    Route::get('/delete_task/{task_id}', [TaskController::class, 'delete_task']); // 
    // Sessions apis
    Route::post('/add_new_session', [SessionController::class, 'addSession']);
    Route::get('/get_sessions_for_case/{case_id}', [SessionController::class, 'get_sessions_for_case']);
    Route::get('/get_sessions_for_lawer', [SessionController::class, 'get_sessions_for_lawer']);
    Route::get('/get_sessions_for_client', [SessionController::class, 'get_sessions_for_client']);
    Route::delete('/delete_session/{session_id}', [SessionController::class, 'delete_session']); // 
    Route::post('/update_session', [SessionController::class, 'update_session']); //
    // Invoices apis
    Route::post('/create_invoice', [InvoiceController::class, 'create_invoice']); //  
    Route::post('/get_invoices_for_client', [InvoiceController::class, 'get_invoices_for_client']); //  
});

Route::prefix('/client_portal/legal_request')->middleware('auth:sanctum')->group(function () {
    Route::post('/create_request', [LegalRequestController::class, 'create']);  //  
    Route::post('/update_request', [LegalRequestController::class, 'update_request']);  //  
    Route::get('/delete_request/{request_id}', [LegalRequestController::class, 'delete_request']);  //  
    Route::get('/get_my_requests', [LegalRequestController::class, 'get_my_requests']); // 
    Route::get('/get_proposals_for_my_requests/{request_id}', [LegalRequestController::class, 'get_proposals']);
    // will make another controller for this in future to make it more organized
    Route::get('/get_offices_or_lawers_available', [LegalRequestController::class, 'get_offices_or_lawers']); // 
    Route::get('/get_protofolio_for_lawer_or_office/{lawer_id}', [LegalRequestController::class, 'get_protofolio_for_lawer_or_office']); // in future not 
});
Route::prefix('/Market_place')->middleware('auth:sanctum')->group(function () {
    Route::get('/get_legal_requests', [ProposalController::class, 'get_legal_requests']);
    Route::get('/get_legal_request_details/{request_id}', [LegalRequestController::class, 'get_legal_request_details']);
    Route::get('/get_my_proposals', [ProposalController::class, 'get_my_proposals']);
    Route::get('/get_proposal_details/{proposal_id}', [ProposalController::class, 'get_proposal_details']);
    Route::post('/send_proposal', [ProposalController::class, 'send_proposal']);
    Route::post('/update_proposal', [ProposalController::class, 'update_proposal']);
    Route::get('/delete_proposal/{proposal_id}', [ProposalController::class, 'delete_proposal']);
});
// notifications apis 
Route::prefix('notifications/')->middleware('auth:sanctum')->group(function () {

    Route::get('/', [NotificationController::class, 'index']);

    Route::get('/{id}/mark-read', [NotificationController::class, 'markAsRead']);

    Route::get('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});
// user managment apis
Route::prefix('ManageUsers/')->middleware('auth:sanctum')->group(function () {
    Route::get('/get_clients', [UserManagmentController::class, 'get_clients']);
});

//  المكتبة القانونية (التشريعات والقوانين)
Route::middleware('auth:sanctum')->prefix('/legislation')->group(function () {
    // المحامي

    Route::get('/Get_legal_categories', [LegislationController::class, 'Get_legal_categories']);
    Route::get('/Get_legislation_with_filter_options', [LegislationController::class, 'index']);
    Route::get('/get_law_details/{law_id}', [LegislationController::class, 'get_law_details']);
    Route::post('/semantic_search', [LegislationController::class, 'semantic_search']);
    // النقابة
    Route::post('/create_law', [LegislationController::class, 'create_law']);
    Route::post('/update_law', [LegislationController::class, 'update_law']);
    Route::get('/delete_law/{law_id}', [LegislationController::class, 'delete_law']);
});
// templates apis
Route::middleware(['auth:sanctum'])->prefix('/templates')->group(function () {
    Route::post('/upload', [TemplateController::class, 'upload']); // Done
    Route::get('/getTemplates', [TemplateController::class, 'getTemplates']); //  Done
    Route::get('/get_templates_categories', [TemplateController::class, 'get_templates_categories']); //  Done
    Route::get('/show_template_details', [TemplateController::class, 'show_template_details']);  //Done
    Route::post('/update_template', [TemplateController::class, 'update_template']); // Done
    Route::get('/delete_template', [TemplateController::class, 'delete_template']); // Done
    Route::post('/generate_template', [TemplateController::class, 'generate_template']); // Done
});

Route::prefix('/dashboard')->middleware('auth:sanctum')->group(function () {

    route::get('/', [DashboardController::class, 'getStatistics']);
    route::get('/alerts', [alertsController::class, 'index']);
});

// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/verify_2FA', [AuthController::class, 'verify_2FA']);
// Route::post('password/forgot', [AuthController::class, 'forgotPassword']);
// Route::post('password/reset', [AuthController::class, 'resetPassword']);
// Route::middleware('auth:api')->group(function () {
//     Route::get('/logout', [AuthController::class, 'logout']);
//     Route::prefix('manage_account/')->group(function () {
//         // personal settings
//         Route::prefix('profile/personal')->group(function () {
//             // get super admin  profile data //  
//             Route::get('super_admin/get_profile', [AuthController::class, 'getSuperAdminProfile']);
//             // update super admin  profile data //  
//             Route::put('super_admin/update_profile', [AuthController::class, 'updateSuperAdminProfile']);
//             // upload personal image profile  //  
//             Route::post('super_admin/upload_avatar', [AuthController::class, 'upload_avatar']);
//             // delete personal image profile and return default image
//             Route::delete('super_admin/delete_image', [AuthController::class, 'deleteImage']);
//         });
//         // security settings
//         Route::prefix('profile/security/')->group(function () {
//             // change pawword 
//             Route::post('change_password', [AuthController::class, 'changePassword']);
//             // disable and enable 2FA
//             Route::get('switch_2FA', [AuthController::class, 'switch_2FA']);
//             // get all active login device (sessions)
//             Route::get('get_sessions', [AuthController::class, 'getSessions']);
//             // delete specific login deveice (session)
//             Route::delete('delete_session/{id}', [AuthController::class, 'deleteSession']);
//             // delete all login devices (sessions)
//             Route::delete('delete_all_sessions', [AuthController::class, 'deleteAllSessions']);
//         });
//         // manage offices 
//         Route::prefix('offices')->group(function () {
//             Route::prefix('office_data')->group(function () {
//                 // create new office
//                 Route::post('create_office', [ManageOfficesController::class, 'createOffice']);
//                 // get all offices
//                 Route::get('get_offices', [ManageOfficesController::class, 'getOffices']);
//                 // get specific office details 
//                 Route::get('get_office/{id}', [ManageOfficesController::class, 'getOffice']);
//                 // update office data 
//                 Route::put('update_office/{id}', [ManageOfficesController::class, 'updateOffice']);
//                 // delete office
//                 Route::delete('delete_office/{id}', [ManageOfficesController::class, 'deleteOffice']);
//             });
//             Route::prefix('office_admins')->group(function () {
//                 // get all office admins
//                 Route::get('get_office_admins', [ManageOfficesController::class, 'getOfficeAdmins']);
//                 // update office admin data 
//                 Route::put('update_office_admin/{id}', [ManageOfficesController::class, 'updateOfficeAdmin']);
//                 // delete office admin
//                 Route::delete('delete_office_admin/{id}', [ManageOfficesController::class, 'deleteOfficeAdmin']);
//             });
//         });
//     });
// });
