<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLawerRequest;
use App\Http\Requests\ListLawyersRequest;
use App\Services\ManageSyndicateUsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ManageSyndicateUsers extends Controller
{
    private ManageSyndicateUsersService $service;
    public function __construct(ManageSyndicateUsersService $service)
    {
        $this->service = $service;
    }

    public function createLawyer(CreateLawerRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $result = $this->service->createLawyer($validatedData);
            return sendResponse("", 'Lawyer created successfully.', 201);
        } catch (\Throwable $th) {
            return sendError('Failed to create lawyer.', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
    public function get_lawers(ListLawyersRequest $request)
    {
        try {
            $vaidated_data = $request->validated();
            $lawyers = $this->service->getLawyers($vaidated_data);
            return sendResponse($lawyers, 'Lawyers retrieved successfully.', 200);
        } catch (\Throwable $th) {
            return sendError('Failed to retrieve lawyers.', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
    public function toggleSuspension($lawyer_id)
    {
        try {
            $validator = Validator::make(['lawyer_id' => $lawyer_id], [
                'lawyer_id' => ['required', 'uuid', Rule::exists('lawyer_profiles', 'id')],
            ]);
            if ($validator->fails()) {
                return sendError('Validation Error :', $validator->errors()->toArray(), 422);
            }
            $this->service->toggleSuspension($lawyer_id);
            return sendResponse(null, 'Lawyer suspension status toggled successfully.', 200);
        } catch (\Throwable $th) {
            return sendError('Failed to toggle lawyer suspension status.', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
    public function promoteTrainee($lawer_id)
    {
        try {
            $validator = Validator::make(['lawyer_id' => $lawer_id], [
                'lawyer_id' => ['required', 'uuid', Rule::exists('lawyer_profiles', 'id')],
            ]);
            if ($validator->fails()) {
                return sendError('Validation Error :', $validator->errors()->toArray(), 422);
            }
            $this->service->promoteTrainee($lawer_id);
            return sendResponse(null, 'Lawyer promoted successfully.', 200);
        } catch (\Throwable $th) {
            return sendError('Failed to promote lawyer.', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
}
