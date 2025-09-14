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
            'name' => 'CreateUsers',
        ]);
        permission::create([
            'id' =>PermissionEnum::UpdateUsers->value,
            'name' => 'UpdateUsers',
        ]);
        permission::create([
            'id' =>PermissionEnum::DeleteUsers->value,
            'name' => 'DeleteUsers',
        ]);
        permission::create([
            'id' =>PermissionEnum::ViewUsers->value,
            'name' => 'ViewUsers',
        ]);
        permission::create([
            'id' =>PermissionEnum::ViewSystemData->value,
            'name' => 'ViewSystemData',
        ]);
        permission::create([
            'id' =>PermissionEnum::AddSystemData->value,
            'name' => 'AddSystemData',
        ]);
        permission::create([
            'id' =>PermissionEnum::UpdateSystemData->value,
            'name' => 'UpdateSystemData',
        ]);
        permission::create([
            'id' =>PermissionEnum::DeleteSystemData->value,
            'name' => 'DeleteSystemData',
        ]);

        $this->adminPermission();
        $this->userPermission();
        $this->viewerPermission();
    }

    // viewer permission
     private function viewerPermission(){
        RolePermission::create([
            'role_id'=>RoleEnum::viewer->value,
            'permission_id'=>PermissionEnum::ViewSystemData->value,
            
        ]);
        RolePermission::create([
            'role_id'=>RoleEnum::viewer->value,
            'permission_id'=>PermissionEnum::ViewUsers->value,
            
        ]);
        RolePermission::create([
            'role_id'=>RoleEnum::viewer->value,
            'permission_id'=>PermissionEnum::UpdateUsers->value,
            
        ]);
     }
     
     // admin permission
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
         RolePermission::create([
            'role_id' =>RoleEnum::Admin->value,
            'permission_id' =>PermissionEnum::AddSystemData->value,
        ]);
          RolePermission::create([
            'role_id' =>RoleEnum::Admin->value,
            'permission_id' =>PermissionEnum::UpdateSystemData->value,
        ]);
        RolePermission::create([
            'role_id' =>RoleEnum::Admin->value,
            'permission_id' =>PermissionEnum::DeleteSystemData->value,
        ]);
       
      
    }
    // user permission
    private function userPermission(){

        RolePermission::create([
            'role_id' =>RoleEnum::User->value,
            'permission_id' =>PermissionEnum::ViewUsers->value,
        ]);
        RolePermission::create([
            'role_id' =>RoleEnum::User->value,
            'permission_id' =>PermissionEnum::ViewSystemData->value,
        ]);
        RolePermission::create([
            'role_id'=>RoleEnum::User->value,
            'permission_id' =>PermissionEnum::UpdateUsers->value,
            
        ]);
         RolePermission::create([
            'role_id'=>RoleEnum::User->value,
            'permission_id' =>PermissionEnum::AddSystemData->value,
            
        ]);
          RolePermission::create([
            'role_id'=>RoleEnum::User->value,
            'permission_id' =>PermissionEnum::UpdateSystemData->value,
            
        ]);
         RolePermission::create([
            'role_id'=>RoleEnum::User->value,
            'permission_id' =>PermissionEnum::DeleteSystemData->value,
            
        ]);
     
    }
    
}
