<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LegalRequestController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::get('/get_sessions_for_client', [SessionController::class, 'get_sessions_for_client']);


Route::prefix('/legal-requests')->group(function () {
    Route::post('/create_request', [LegalRequestController::class, 'create']);
    Route::post('/update_request', [LegalRequestController::class, 'update_request']);
    Route::delete('/delete_request/{request_id}', [LegalRequestController::class, 'delete_request']);
    Route::get('/get_my_requests', [LegalRequestController::class, 'get_my_requests']);
    Route::get('/get_proposals_for_my_requests/{request_id}', [LegalRequestController::class, 'get_proposals']);
});
Route::get('/providers', [LegalRequestController::class, 'get_offices_or_lawers']);
Route::get('/providers/{lawyer_id}/portfolio', [LegalRequestController::class, 'get_protofolio_for_lawer_or_office']);

Route::get('/sessions', [SessionController::class, 'get_sessions_for_client']);
Route::get('/invoices', [InvoiceController::class, 'get_invoices_for_client']);

// التوقيع الإلكتروني — طرف العميل فقط (طلب التوقيع نفسه يبدأه المحامي بـ lawyer.php)
// Route::prefix('documents/{document_id}/signature')->group(function () {
//     Route::post('/sign', [DocumentSignatureController::class, 'sign']);
//     Route::get('/status', [DocumentSignatureController::class, 'status']);
// });
