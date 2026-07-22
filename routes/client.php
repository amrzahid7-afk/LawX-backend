<?php

use App\Http\Controllers\SignatureController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LegalRequestController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserManagmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('/legal-requests')->group(function () {
    Route::post('/create_request', [LegalRequestController::class, 'create']);
    Route::post('/update_request', [LegalRequestController::class, 'update_request']);
    Route::delete('/delete_request/{request_id}', [LegalRequestController::class, 'delete_request']);
    Route::get('/get_my_requests', [LegalRequestController::class, 'get_my_requests']);
    Route::get('/get_proposals_for_my_requests/{request_id}', [ProposalController::class, 'get_proposals']);
});

Route::get('/providers', [UserManagmentController::class, 'get_offices_or_lawers']);
Route::get('/providers/{lawyer_id}/portfolio', [UserManagmentController::class, 'get_protofolio_for_lawer_or_office']);

Route::get('/sessions', [SessionController::class, 'get_sessions_for_client']);
Route::get('/invoices', [InvoiceController::class, 'get_invoices_for_client']);

// التوقيع الإلكتروني — طرف العميل فقط (طلب التوقيع نفسه يبدأه المحامي بـ lawyer.php)
// Route::prefix('documents/{document_id}/signature')->group(function () {
//     Route::post('/sign', [DocumentSignatureController::class, 'sign']);
//     Route::get('/status', [DocumentSignatureController::class, 'status']);
// });

// signature
Route::prefix('electonic_signature')->group(function () {
    Route::get('/get_signing_url/{signature_request_id}', [SignatureController::class, 'getSigningUrl']);
    Route::get('/get_files_require_sign', [SignatureController::class, 'getClientFiles']);
    Route::get('/review_file/{signature_request_id}', [SignatureController::class, 'reviewFile']);
});
