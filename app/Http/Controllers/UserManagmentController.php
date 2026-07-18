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
        return response()->json([
            'status' => "success",
            'clients' => $clients
        ], 200);
    }
    public function get_offices_or_lawers(Request $request)
    {
        try {
            $data = $this->userManagmentService->get_offices_or_lawers_available($request);
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine(),
                'class' => $e->getFile()
            ], 500);
        }
    }
    public function get_protofolio_for_lawer_or_office($lawer_id)
    {
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
                return response()->json([
                    'message' => 'Invalid Lawyer/Office ID',
                    'error' => $validator->errors()->first()
                ], 422);
            }
            $data = $this->userManagmentService->getProtofolioForLawerOrOffice($lawer_id);
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine(),
                'class' => $e->getFile()
            ], 500);
        }
    }
}
