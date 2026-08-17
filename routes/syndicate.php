<?php

use App\Http\Controllers\Api\LegislationController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\ManageSyndicateUsers;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SignatureController;
use Illuminate\Support\Facades\Route;

// syndicate Apis
// --------------------------------------------------------------------------
// Api :  Add New Lawer For System 
Route::post('/createLawyer', [ManageSyndicateUsers::class, 'createLawyer']);
// Api :  get all lawers in system
Route::get('/get_lawers', [ManageSyndicateUsers::class, 'get_lawers']);
// Api : make specific lawers suspended or unsuspended
Route::patch('/lawyers/{lawyer_id}/suspend', [ManageSyndicateUsers::class, 'toggleSuspension']);
// Api : make specific lawers from trainee to licsend
Route::patch('/lawyers/{lawyer_id}/promote', [ManageSyndicateUsers::class, 'promoteTrainee']);
// Api : Update specific lawyer details
Route::put('/lawyers/{lawyer_id}/update', [ManageSyndicateUsers::class, 'updateLawyer']);
// Api : Delete specific lawyer (Soft Delete)
Route::delete('/lawyers/{lawyer_id}/delete', [ManageSyndicateUsers::class, 'deleteLawyer']);

// tamplates Apis 
Route::prefix('templates')->group(function () {
    Route::post('/upload', [TemplateController::class, 'upload']);
    Route::post('/update_template', [TemplateController::class, 'update_template']);
    Route::get('/getTemplates', [TemplateController::class, 'getTemplates']);
    Route::delete('/delete_template', [TemplateController::class, 'delete_template']);

    Route::get('/getTemplates', [TemplateController::class, 'getTemplates']);
    Route::get('/get_templates_categories', [TemplateController::class, 'get_templates_categories']);
    Route::get('/show_template_details', [TemplateController::class, 'show_template_details']);
});
//Electonic signature module 
Route::prefix('electonic_signature')->group(function () {
    Route::get('/get_signing_url/{signature_request_id}', [SignatureController::class, 'getSigningUrl']);
    Route::get('/get_files_require_sign', [SignatureController::class, 'getSyndicateFiles']);
    Route::get('/review_file/{signature_request_id}', [SignatureController::class, 'reviewFile']);
});

// Manage Laws Apis 
Route::prefix('legislation')->group(function () {
    Route::post('/create_law', [LegislationController::class, 'create_law']);
    Route::post('/update_law', [LegislationController::class, 'update_law']);
    Route::delete('/delete_law/{law_id}', [LegislationController::class, 'delete_law']);
    Route::get('/get_laws', [LegislationController::class, 'index']);

    Route::get('/Get_legal_categories', [LegislationController::class, 'Get_legal_categories']);
    Route::get('/get_law_details/{law_id}', [LegislationController::class, 'get_law_details']);
    Route::get('/semantic_search', [LegislationController::class, 'semantic_search']);
});
Route::prefix('/profile')->group(function () {
    Route::get('get_my_profile', [ProfileController::class, 'getMyProfile']);
    Route::post('update_my_profile', [ProfileController::class, 'update_my_profile']);
});
