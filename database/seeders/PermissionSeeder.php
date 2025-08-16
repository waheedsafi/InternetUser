<?php

namespace Database\Seeders;

use App\Enum\PermissionEnum;
use App\Enum\RoleEnum;
use App\Models\permission;
use App\Models\RolePermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        permission::create([
            'id' =>PermissionEnum::CreateUsers->value,
            'name' => 'Create User',
        ]);
        permission::create([
            'id' =>PermissionEnum::UpdateUsers->value,
            'name' => 'Create User',
        ]);
        permission::create([
            'id' =>PermissionEnum::DeleteUsers->value,
            'name' => 'Create User',
        ]);
        permission::create([
            'id' =>PermissionEnum::ViewUsers->value,
            'name' => 'Create User',
        ]);
        permission::create([
            'id' =>PermissionEnum::ViewSystemData->value,
            'name' => 'Create User',
        ]);
        permission::create([
            'id' =>PermissionEnum::AddSystemData->value,
            'name' => 'Create User',
        ]);
        permission::create([
            'id' =>PermissionEnum::UpdateSystemData->value,
            'name' => 'Create User',
        ]);
        permission::create([
            'id' =>PermissionEnum::DeleteSystemData->value,
            'name' => 'Create User',
        ]);

        $this->adminPermission();
        $this->userPermission();
    }

    private function adminPermission(){

        RolePermission::create([
            'role_id' =>RoleEnum::Admin->value,
            'permission_id' =>PermissionEnum::CreateUsers->value,
        ]);
        RolePermission::create([
            'role_id' =>RoleEnum::Admin->value,
            'permission_id' =>PermissionEnum::UpdateUsers->value,
        ]);
    
        RolePermission::create([
            'role_id' =>RoleEnum::Admin->value,
            'permission_id' =>PermissionEnum::DeleteUsers->value,
        ]);
      
        RolePermission::create([
            'role_id' =>RoleEnum::Admin->value,
            'permission_id' =>PermissionEnum::ViewUsers->value,
        ]);
      
    }
    private function userPermission(){

        RolePermission::create([
            'role_id' =>RoleEnum::User->value,
            'permission_id' =>PermissionEnum::ViewUsers->value,
        ]);
        RolePermission::create([
            'role_id' =>RoleEnum::User->value,
            'permission_id' =>PermissionEnum::ViewSystemData->value,
        ]);
     
    }
}
