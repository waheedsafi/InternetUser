<?php

namespace Database\Seeders;

use App\Enum\RoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       Role::create([
            'id' => RoleEnum::Admin->value,
            'name' => 'Admin',
        ]);
        $adminExists = User::where('role_id', RoleEnum::Admin->value)->exists();

        if (!$adminExists) {
            
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role_id' => RoleEnum::Admin->value,
                'password' => Hash::make('adminpassword'), 
            ]);
        }
    }
}
