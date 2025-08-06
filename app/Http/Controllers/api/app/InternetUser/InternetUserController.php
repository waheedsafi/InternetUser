<?php

namespace App\Http\Controllers\api\app\InternetUser;

use App\Models\InternetUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\InternetUserDevice;

class InternetUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
$data= DB::table('internet_users as intu')
    ->join('persons as per', 'per.id', '=', 'intu.person_id')
    ->join('directorates as dir', 'dir.id', '=', 'per.directorate_id')
     ->join('employment_types as emp', 'emp.id', '=', 'per.employment_type_id')
     ->join('internet_user_devices as user', 'user.internet_user_id', '=', 'intu.id')
     ->join('device_types as dt', 'user.device_type_id', '=', 'dt.id')  
    ->leftJoin('directorates as parent_dir', 'parent_dir.id', '=', 'dir.directorate_id')  
    ->leftJoin('violations as val', 'val.internet_user_id', '=', 'intu.id')
    ->select(
        'intu.id',
        'intu.mac_address',
        'emp.name as employment_type',
        'per.name',
        'intu.device_limit',
        'per.email',
        'per.lastname',
        'intu.username',
        'per.phone',
        'dir.name as directorate',  
        'intu.status',
        'per.position',
         'dt.name as device_type',
        DB::raw('COUNT(val.id) as violations_count'),  
        'parent_dir.name as deputy'  
    )
    ->groupBy(
        'intu.id',
        'intu.mac_address',
        'emp.name',
        'intu.device_limit',
        'per.name',
        'per.email',
        'per.lastname',
        'intu.username',
        'per.phone',
        'per.directorate_id',
        'intu.status',
        'dir.name',
        'parent_dir.name',
        'per.position', 
        'dt.name',
    )
    ->get();
    

 return response()->json($data);

    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    
    DB::beginTransaction();

    try {
        
        $validated = $request->validate([
            'username' => 'required|string|unique:internet_users,username',
            'status' => 'required|in:0,1',
            'phone' => 'required|string|max:15|unique:persons,phone',
            'directorate_id' => 'required|exists:directorates,id',
            'email' => 'required|unique:persons,email', 
            'employee_type_id' => 'required|exists:employment_types,id', 
            'mac_address' => 'nullable|unique:internet_users,mac_address', 
        ]);

        
     
        $person = Person::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $validated['email'],  
            'phone' => $validated['phone'],
            'position' => $request->position,
            'directorate_id' => $validated['directorate_id'],
            'employment_type_id' => $validated['employee_type_id'], 
        ]);

        
        $internetUser = InternetUser::create([
            'person_id' => $person->id,
            'username' => $validated['username'],
            'status' => $validated['status'],
            'phone' => $validated['phone'],
            'directorate_id' => $validated['directorate_id'],
            'device_limit' => $request->device_limit,
            'mac_address' => $validated['mac_address'],
        ]);
        InternetUserDevice::create([
            'internet_user_id' => $internetUser->id,
            'device_type_id' => $request->device_type_id,
        ]);     
        
        DB::commit();

        return response()->json([
            'message' => 'Internet user successfully created.',
            'data' => $internetUser,
        ], 201);
    } catch (\Exception $e) {
        
        DB::rollBack();

        
        return response()->json([
            'message' => 'An error occurred while creating the user.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
         try {
            
            $internetUser = InternetUser::with('person',
             'person.directorate',
              'person.position', 
              'person.employmentType')
                ->findOrFail($id);

            return response()->json([
                'message' => 'Internet user found.',
                'data' => $internetUser,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internet user not found.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
           
            $validated = $request->validate([
                'username' => 'required|string|unique:internet_users,username,' . $id,  
                'status' => 'required|in:0,1',
                'phone' => 'required|string|max:15|unique:persons,phone,' . $id,  
                'directorate_id' => 'required|exists:directorates,id',
                'email' => 'required|unique:persons,email,' . $id, 
                'employee_type_id' => 'required|exists:employment_types,id',
                'position' => 'required|exists:positions,id',
                'person_id' => 'required|exists:persons,id',
                'device_limit' => 'required|exists:internet_users,device_limit',
                'mac_address' => 'nullable|exists:internet_users,mac_address',
                'device_type_id' => 'required|device_type,id',
            ]);

            
            $internetUser = InternetUser::findOrFail($id);
            $person = $internetUser->person;  

         
            $person->update([
                'name' => $request->name,
                'lastname' => $request->lastname,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'position' => $validated['position'],
                'directorate_id' => $validated['directorate_id'],
                'employment_type_id' => $validated['employee_type_id'],
            ]);

            
            $internetUser->update([
                'username' => $validated['username'],
                'status' => $validated['status'],
                'phone' => $validated['phone'],
                'directorate_id' => $validated['directorate_id'],
                'device_limit' => $validated['device_limit'],
                'mac_address' => $validated['mac_address'],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Internet user successfully updated.',
                'data' => $internetUser,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while updating the user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         DB::beginTransaction();

        try {
            
            $internetUser = InternetUser::findOrFail($id);
            $person = $internetUser->person;
            $internetUser->delete();
            $person->delete();

            DB::commit();

            return response()->json([
                'message' => 'Internet user and associated person successfully deleted.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while deleting the user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getTotalUsers()
{
    
    $totalUsers = InternetUser::count();
    return response()->json([
        'total_users' => $totalUsers,
    ]);
}



public function updateStatus(Request $request, $id)
{
    $internetUser = InternetUser::find($id);
    if (!$internetUser) {
        return response()->json([
            'message' => 'User not found.',
        ], 404);
    }
    $internetUser->status = $internetUser->status == 1 ? 0 : 1;
    $internetUser->save();
    return response()->json([
        'message' => 'User status updated successfully.',
        'status' => $internetUser->status,
    ]);
}

    }
