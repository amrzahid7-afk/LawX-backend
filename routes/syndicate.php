<?php

use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\ManageSyndicateUsers;
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
// tamplates Apis 
Route::post('/upload', [TemplateController::class, 'upload']);
Route::get('/getTemplates', [TemplateController::class, 'getTemplates']);
Route::patch('/update_template', [TemplateController::class, 'update_template']);
Route::delete('/delete_template', [TemplateController::class, 'delete_template']);
