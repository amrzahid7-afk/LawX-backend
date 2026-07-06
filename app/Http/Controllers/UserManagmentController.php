<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagmentController extends Controller
{
    public function get_clients()
    {
        $client_role_id = Role::where('slug', 'client')->first()->id;
        $clients  = User::whereRoleId($client_role_id)->get();
        return response()->json([
            'status' => "success",
            'clients' => $clients
        ], 200);
    }
}
