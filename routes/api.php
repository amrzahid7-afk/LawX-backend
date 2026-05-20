<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LegalRequestController;
use App\Http\Controllers\ManageOfficesController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TaskController;
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

Route::get('/test', function () {
    return "API Working";
});
Route::prefix('/Auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);  // done 
    Route::post('password/forgot', [AuthController::class, 'forgotPassword']); // done
    Route::post('password/reset', [AuthController::class, 'resetPassword']); // done
    Route::middleware('auth:api')->group(function () {
        Route::get('/logout', [AuthController::class, 'logout']); // done
    });
});
Route::prefix('/Case')->group(function () {
    Route::post('/create_case', [CaseController::class, 'create']);
    Route::post('/update_case',  [CaseController::class, 'update']);
    Route::get('/get_cases', [CaseController::class, 'view_list']);
    Route::post('/add_new_session', [SessionController::class, 'addSession']);
    Route::get('/get_sessions_for_case/{case_id}', [SessionController::class, 'get_sessions_for_case']);
    Route::get('/get_sessions_for_lawer', [SessionController::class, 'get_sessions_for_lawer']);
    Route::get('/get_sessions_for_client', [SessionController::class, 'get_sessions_for_client']);
    Route::post('/upload_document', [DocumentController::class, 'upload_Document']);
    Route::get('/get_documents_for_case/{case_id}', [DocumentController::class, 'get_sessions_for_case']);
    Route::get('/get_tasks_for_case/{case_id}', [TaskController::class, 'get_tasks_for_case']);
    Route::post('/create_task', [TaskController::class, 'create_task']);
})->middleware('auth:api');
Route::prefix('/client_portal/legal_request')->group(function () {
    Route::post('/create_request', [LegalRequestController::class, 'create']);  // done 
    Route::get('/get_my_requests', [LegalRequestController::class, 'get_my_requests']); // done
    Route::get('/get_offices_or_lawers_available', [LegalRequestController::class, 'get_offices_or_lawers']); // done
    Route::get('/get_protofolio_for_lawer_or_office/{ID}', [LegalRequestController::class, 'get_protofolio']); // in future not done
    Route::get('/get_proposals_for_my_requests/{request_id}', [LegalRequestController::class, 'get_proposals']);
})->middleware('auth:api');
Route::prefix('/Market_place')->group(function () {
    Route::get('/get_legal_requests', [ProposalController::class, 'get_legal_requests']);
    Route::post('/send_proposal', [ProposalController::class, 'send_proposal']);
})->middleware('auth:api');
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/verify_2FA', [AuthController::class, 'verify_2FA']);
// Route::post('password/forgot', [AuthController::class, 'forgotPassword']);
// Route::post('password/reset', [AuthController::class, 'resetPassword']);
// Route::middleware('auth:api')->group(function () {
//     Route::get('/logout', [AuthController::class, 'logout']);
//     Route::prefix('manage_account/')->group(function () {
//         // personal settings
//         Route::prefix('profile/personal')->group(function () {
//             // get super admin  profile data //  done
//             Route::get('super_admin/get_profile', [AuthController::class, 'getSuperAdminProfile']);
//             // update super admin  profile data //  done
//             Route::put('super_admin/update_profile', [AuthController::class, 'updateSuperAdminProfile']);
//             // upload personal image profile  //  done
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
