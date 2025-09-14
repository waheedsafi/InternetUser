<?php
 namespace Database\Seeders;
 use App\Models\User;
use App\Models\Directorate;
use App\Models\EmploymentType;
use App\Models\DirectorateType;
use Illuminate\Database\Seeder;
use App\Enum\DeputyMinistryEnum;
use App\Enum\DeviceTypeEnum;
use App\Enum\DirectorateTypeEnum;
use App\Enum\EmploymentTypeEnum;
use App\Enum\GroupEnum;
use App\Enum\PermissionEnum;
use App\Enum\RoleEnum;
use App\Models\DeviceType;
use App\Models\Group;
use App\Models\permission;
use App\Models\Role;
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

        DeviceType::create([
            'id' => DeviceTypeEnum::Desktop->value,
            'name' => 'Desktop'
        ]);
        DeviceType::create([
            'id' => DeviceTypeEnum::Laptop->value,
            'name' => 'Laptop '
        ]);
        DeviceType::create([
            'id' => DeviceTypeEnum::Tablet->value,
            'name' => 'Tablet'
        ]);
        DeviceType::create([
            'id'=>DeviceTypeEnum::Mobile->value,
            'name'=>'Mobile'
        ]);
        DeviceType::create([
            'id'=>DeviceTypeEnum::AllInOne->value,
            'name'=>'All In One'
        ]);

        Group::create([
            'id' => GroupEnum::OpenGroup->value,
            'name' => 'OpenGroup'
        ]);
        Group::create([
            'id' => GroupEnum::Clientless->value,
            'name' => 'Clientless'
        ]);
        Group::create([
            'id' => GroupEnum::Employee->value,
            'name' => 'Employee'
        ]);

        // User Permissions

    

        EmploymentType::create([
            'id' => EmploymentTypeEnum::NTA->value,
            'name' => 'NTA'
        ]);
        EmploymentType::create([
            'id' => EmploymentTypeEnum::Permanent->value,
            'name' => 'Permanent'
        ]);
        EmploymentType::create([
            'id' => EmploymentTypeEnum::TempContract->value,
            'name' => 'TempContract'
        ]);
        $this->deputyMinistry();

        Role::create([
            'id' => RoleEnum::Admin->value,
            'name' => 'Admin'
        ]);
        Role::create([
            'id' => RoleEnum::User->value,
            'name' => 'User'
        ]);
        Role::create([
            'id' => RoleEnum::viewer->value,
            'name' => 'viewer'
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
        $this->call(PermissionSeeder::class);
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
