<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserManagmentController extends Controller
{
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
}
