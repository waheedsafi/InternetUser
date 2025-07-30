<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Enum\RoleEnum;
use App\Models\Directorate;
use App\Models\EmploymentType;
use App\Models\DirectorateType;
use Illuminate\Database\Seeder;
use App\Enum\DeputyMinistryEnum;
use App\Enum\DirectorateTypeEnum;
use App\Enums\EmploymentTypeEnum;
use Egulias\EmailValidator\EmailParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
      

        EmploymentType::create([
            'id' => EmploymentTypeEnum::NTA->value,
            'name'=> 'NTA'
        ]);
        EmploymentType::create([
            'id'=> EmploymentTypeEnum::Permanent->value,
            'name' => 'Permanent'
        ]);
        EmploymentType::create([
            'id' => EmploymentTypeEnum::TempContract->value,
            'name' => 'TempContract'
        ]);
        $this->deputyMinistry();   


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
        $this->call(DirectorateSeeder::class);
    }

    
   private function deputyMinistry()
{
    
    DirectorateType::create([
        'id' => DirectorateTypeEnum::Ministry->value,
        'name' => 'Authority'
    ]);
    DirectorateType::create([
        'id' => DirectorateTypeEnum::Directorate->value,
        'name' => 'Directorate'
    ]);

    
    $ministry = Directorate::create([
        'id' => DeputyMinistryEnum::DeputyMinistry->value,
        'name' => 'Ministry',
        'directorate_type_id' => DirectorateTypeEnum::Ministry->value,
        'directorate_id' => null,
    ]);


    Directorate::create([
        'id' => DeputyMinistryEnum::AdministrativeAndFinancial->value,
        'name' => 'Administrative And Financial Deputy Ministry',
        'directorate_type_id' => DirectorateTypeEnum::Ministry->value,
        'directorate_id' => $ministry->id,
    ]);

    Directorate::create([
        'id' => DeputyMinistryEnum::PlanAndPolicy->value,
        'name' => 'Plan And Policy Deputy Ministry',
        'directorate_type_id' => DirectorateTypeEnum::Ministry->value,
        'directorate_id' => $ministry->id,
    ]);

    Directorate::create([
        'id' => DeputyMinistryEnum::DrugAndFood->value,
        'name' => 'Drug And Food Deputy Ministry',
        'directorate_type_id' => DirectorateTypeEnum::Ministry->value,
        'directorate_id' => $ministry->id,
    ]);

    Directorate::create([
        'id' => DeputyMinistryEnum::ServiceProviding->value,
        'name' => 'Service Providing Deputy Ministry',
        'directorate_type_id' => DirectorateTypeEnum::Ministry->value,
        'directorate_id' => $ministry->id,
    ]);
}
}
