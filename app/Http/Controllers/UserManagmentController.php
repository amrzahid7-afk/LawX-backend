<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\UserManagmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UserManagmentController extends Controller
{
    private UserManagmentService $userManagmentService;

    public function __construct(UserManagmentService $userManagmentService)
    {
        $this->userManagmentService = $userManagmentService;
    }
    public function get_clients()
    {
        // validate permission
        if (Gate::denies('get_clients', User::class)) {
            return sendError("Unauthorized , You do not have permission to view clients ", [], 403);
        }
        $client_role_id = Role::where('slug', 'client')->first()->id;
        $clients  = User::whereRoleId($client_role_id)->get();
        return sendResponse($clients, 'تم الحصول على البيانات', 200);
    }
    public function get_offices_or_lawers(Request $request)
    {
        try {
            $data = $this->userManagmentService->get_offices_or_lawers_available($request);
            return sendResponse($data, 'تم الحصول على البيانات', 200);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }
    public function get_protofolio_for_lawer_or_office($lawer_id)
    {
        //  validate  data 
        try {
            $validator = Validator::make(
                ['lawer_id' => $lawer_id],
                [
                    'lawer_id' => ['required', 'exists:users,id'],
                ],
                [
                    'lawer_id.required' => 'Lawyer/Office ID is required',
                    'lawer_id.exists' => 'Lawyer/Office ID does not exist',
                ]
            );
            if ($validator->fails()) {
                return sendError('Validation failed', $validator->errors()->toArray(), 422);
            }
            // call service
            $data = $this->userManagmentService->getProtofolioForLawerOrOffice($lawer_id);
            // send response
            return sendResponse($data, 'تم الحصول على البيانات', 200);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }
}
