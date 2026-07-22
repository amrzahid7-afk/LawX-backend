<?php

use App\Http\Controllers\Api\LegislationController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LegalRequestController;
use App\Http\Controllers\ManageOfficeController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserManagmentController;
use Illuminate\Support\Facades\Route;


// Manage Offices
Route::prefix('/Manage_Offices')->group(function () {
    // api to make lawer can manage the lawers in his office 
    Route::post('/activate_office', [ManageOfficeController::class, 'activate_office']);
    // api for invite another lawer to join the office
    Route::post('/invite_lawer_to_office', [ManageOfficeController::class, 'invite_lawer_to_office']);
    // api for My sended invitations to other lawers to join my office (Here I am the owner of the office)
    Route::get('/my_sended_invitations', [ManageOfficeController::class, 'my_sended_invitations']);
    // api for My received invitations from other lawers to join their offices (Here I am the invited lawer to join offices & I dont have Office )
    Route::get('/my_received_invitations', [ManageOfficeController::class, 'my_received_invitations']);
    // api for remove invitation that I sent to another lawer to join my office (Here I am the owner of the office)
    Route::post('/remove_invitation', [ManageOfficeController::class, 'remove_invitation']);

    // api for accept invitation to join an office
    Route::post('/accept_invitation', [ManageOfficeController::class, 'accept_invitation']);
    // api for reject invitation to join an office
    Route::post('/reject_invitation', [ManageOfficeController::class, 'reject_invitation']);
    // api for remove a lawer from my office (Here I am the owner of the office)
    Route::post('/remove_lawer_from_office', [ManageOfficeController::class, 'remove_lawer_from_office']);

    // api for get all lawers in my office (Here I am the owner of the office)
    Route::get('/get_all_lawers_in_my_office', [ManageOfficeController::class, 'get_all_lawers_in_my_office']);
});

// cases
Route::prefix('/Case')->group(function () {
    Route::post('/create_case', [CaseController::class, 'create']);
    Route::post('/update_case', [CaseController::class, 'update']);
    Route::get('/get_cases', [CaseController::class, 'view_list']);
    Route::get('/get_case_details/{case_id}', [CaseController::class, 'get_case_details']);
    Route::delete('/delete_case/{case_id}', [CaseController::class, 'delete_case']);
});

// docs
Route::prefix('documents')->group(function () {
    Route::post('/upload_document', [DocumentController::class, 'upload_Document']);
    Route::get('/get_documents_for_case/{case_id}', [DocumentController::class, 'get_documents_for_case']);
    Route::get('/delete_document/{document_id}', [DocumentController::class, 'delete_document']);
    Route::get('/{document_id}/versions', [DocumentController::class, 'get_document_versions']);

    //  Electonic Signature Module
    Route::prefix('electonic_signature')->group(function () {
        // Api : Lawyer Request Signature 
        Route::post('/signature-request', [SignatureController::class, 'lawyerRequestSignature']);
        // Api : Get Signing Url To Sign the Document
        Route::get('/get-signing-url/{signature_request_id}', [SignatureController::class, 'getSigningUrl']);
        // Fiels uploaded By ME To Sign
        Route::get('/get_files_uploaded_by_Me_To_sign', [SignatureController::class, 'getLawyerFiles']);
        // Files require Sign By Me
        Route::get('/get_files_require_sign', [SignatureController::class, 'get_files_require_sign']);
        // Review File Befor signing  
        Route::get('/review_file_to_sign/{signature_request_id}', [SignatureController::class, 'reviewFile']);
    });
});
// tasks 
Route::prefix('tasks')->group(function () {
    Route::post('/create_task', [TaskController::class, 'create_task']);
    Route::get('/get_my_tasks', [TaskController::class, 'get_my_tasks']);
    Route::get('/get_tasks_for_case/{case_id}', [TaskController::class, 'get_tasks_for_case']);
    Route::post('/edit_task', [TaskController::class, 'edit_task']);
    Route::get('/delete_task/{task_id}', [TaskController::class, 'delete_task']);
});
// sessions 
Route::prefix('sessions')->group(function () {
    Route::post('/add_new_session', [SessionController::class, 'addSession']);
    Route::get('/get_sessions_for_case/{case_id}', [SessionController::class, 'get_sessions_for_case']);
    Route::get('/get_sessions_for_lawer', [SessionController::class, 'get_sessions_for_lawer']);
    Route::post('/update_session', [SessionController::class, 'update_session']);
    Route::delete('/delete_session/{session_id}', [SessionController::class, 'delete_session']);
});
// invoices 
Route::post('/create_invoice', [InvoiceController::class, 'create_invoice']);
//     // Invoices apis
//     Route::post('/create_invoice', [InvoiceController::class, 'create_invoice']); //  
//     Route::post('/get_invoices_for_client', [InvoiceController::class, 'get_invoices_for_client']); //  

// Market 
Route::prefix('/Market_place')->group(function () {
    Route::get('/get_legal_requests', [LegalRequestController::class, 'get_legal_requests']);
    Route::get('/get_legal_request_details/{request_id}', [LegalRequestController::class, 'get_legal_request_details']);
    Route::get('/get_my_proposals', [ProposalController::class, 'get_my_proposals']);
    Route::get('/get_proposal_details/{proposal_id}', [ProposalController::class, 'get_proposal_details']);
    Route::post('/send_proposal', [ProposalController::class, 'send_proposal']);
    Route::post('/update_proposal', [ProposalController::class, 'update_proposal']);
    Route::get('/delete_proposal/{proposal_id}', [ProposalController::class, 'delete_proposal']);
});

Route::get('/get_clients', [UserManagmentController::class, 'get_clients']);


// Laws Apis
Route::prefix('legislation')->group(function () {
    Route::get('/Get_legal_categories', [LegislationController::class, 'Get_legal_categories']);
    Route::get('/Get_legislation_with_filter_options', [LegislationController::class, 'index']);
    Route::get('/get_law_details/{law_id}', [LegislationController::class, 'get_law_details']);
    Route::post('/semantic_search', [LegislationController::class, 'semantic_search']);
});

// templates Apis
Route::prefix('templates')->group(function () {
    Route::get('/getTemplates', [TemplateController::class, 'getTemplates']);
    Route::get('/get_templates_categories', [TemplateController::class, 'get_templates_categories']);
    Route::get('/show_template_details', [TemplateController::class, 'show_template_details']);
    Route::post('/generate_template', [TemplateController::class, 'generate_template']);
});
