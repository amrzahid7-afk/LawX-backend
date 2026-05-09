<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdminCommand extends Command
{
    protected $signature = 'create-super-admin {username} {password} {email}';

    protected $description = 'Build a backend CLI command that allows administrators to create a new Super Admin account via the terminal, ensuring secure input and validation.';

    public function handle()
    {
        $validator = Validator::make([
            'username' => $this->argument('username'),
            'password' => $this->argument('password'),
            'email' => $this->argument('email'),
        ], [
            'username' => 'required',
            'password' => 'required|min:8',
            'email' => 'required|email|unique:users',
        ]);

        if ($validator->fails()) {
            $this->error('Invalid input. Please provide a username and password.');
            return;
        }

        $username = $this->argument('username');
        $password = $this->argument('password');
        $email = $this->argument('email');


        // Store data
        $user =  User::create([
            'office_id' => null,
            'role_id' => Role::where('name', 'productowner')->first()->id,
            'email' => $email,
            'password' => bcrypt($password),
            'full_name' => $username,
            'is_active' => true
        ]);
        // create Super admin record 
        SuperAdmin::create([
            'user_id' => $user->id,
        ]);
        $this->info('Super Admin created successfully.');
    }
}
