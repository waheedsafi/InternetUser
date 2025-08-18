<?php

namespace App\Http\Controllers\api\app\InternetUser;

use App\Models\InternetUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Person;
use App\Models\InternetUserDevice;
use Illuminate\Support\Facades\Log;

class InternetUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {

        $violationCounts = DB::table('violations')
            ->select('internet_user_id', DB::raw('COUNT(*) as total_violations'))
            ->groupBy('internet_user_id');

        $data = DB::table('internet_users as intu')
            ->join('persons as per', 'per.id', '=', 'intu.person_id')
            ->join('directorates as dir', 'dir.id', '=', 'per.directorate_id')
            ->join('employment_types as emp', 'emp.id', '=', 'per.employment_type_id')
            ->join('internet_user_devices as user', 'user.internet_user_id', '=', 'intu.id')
            ->join('device_types as dt', 'user.device_type_id', '=', 'dt.id')
            ->join('groups as gr', 'gr.id', '=', 'intu.group_id')
            ->leftJoin('directorates as parent_dir', 'parent_dir.id', '=', 'dir.directorate_id')
            ->leftJoin('violations as val', function ($join) {
                $join->on('val.internet_user_id', '=', 'intu.id')
                    ->whereRaw('val.id = (
                     SELECT MAX(v2.id) 
                     FROM violations v2 
                     WHERE v2.internet_user_id = intu.id
                 )');
            })
            ->leftJoin('violations_types as valt', 'val.violation_type_id', '=', 'valt.id')
            ->leftJoinSub($violationCounts, 'vc', function ($join) {
                $join->on('vc.internet_user_id', '=', 'intu.id');
            })
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
                'gr.name as groups',
                'val.comment',
                'valt.name as violation_type',
                DB::raw('COALESCE(vc.total_violations, 0) as violations_count'),
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
                'val.comment',
                'valt.name',
                'gr.name',
                'vc.total_violations'

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



        $validated = $request->validate([
            'username' => 'required|string|unique:internet_users,username',
            'status' => 'required|in:0,1',
            'phone' => 'required|string|max:15|unique:persons,phone',
            'directorate_id' => 'required|exists:directorates,id',
            'email' => 'required|unique:persons,email',
            'employee_type_id' => 'required|exists:employment_types,id',
            'mac_address' => 'nullable|unique:internet_users,mac_address',
            'group_id' => 'required|exists:groups,id',
            'position' => 'required|string',
            // 'device_type_ids' => 'required|array',

        ]);



        $person = Person::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'position' => $validated['position'],
            'directorate_id' => $validated['directorate_id'],
            'employment_type_id' => $validated['employee_type_id'],
        ]);


        $internetUser = InternetUser::create([
            'person_id' => $person->id,
            'group_id' => $validated['group_id'],
            'username' => $validated['username'],
            'status' => $validated['status'],
            'phone' => $validated['phone'],
            'device_limit' => $request->device_limit,
            'mac_address' => $validated['mac_address'],
        ]);
        InternetUserDevice::create([
            'internet_user_id' => $internetUser->id,
            'device_type_id' => $request->device_type_id,
        ]);

        // $internetUser->deviceTypes()->sync($validated['device_type_ids']);



        DB::commit();

        return response()->json([
            'message' => 'Internet user successfully created.',
            'data' => $internetUser,
        ], 201);


        DB::rollBack();


        return response()->json([
            'message' => 'An error occurred while creating the user.',
            'error' => $e->getMessage(),
        ], 500);
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

        $internetUser = InternetUser::with('person', 'person.directorate', 'person.employmentType')
            ->findOrFail($id);

        return response()->json([
            'message' => 'Internet user found.',
            'data' => $internetUser,
        ], 200);


        return response()->json([
            'message' => 'Internet user not found.',
            'error' => $e->getMessage(),
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $internetUser = InternetUser::findOrFail($id);
        $person = $internetUser->person;

        $validated = $request->validate([
            'username' => 'required|string|unique:internet_users,username,' . $internetUser->id,
            'status' => 'required|in:0,1',
            'phone' => 'required|string|max:15|unique:persons,phone,' . $person->id,
            'directorate_id' => 'required|exists:directorates,id',
            'email' => 'required|email|unique:persons,email,' . $person->id,
            'employee_type_id' => 'required|exists:employment_types,id',
            'position' => 'required|string',
            'device_limit' => 'required|integer',
            'mac_address' => 'nullable|unique:internet_users,mac_address,' . $internetUser->id,
            'device_type_id' => 'required|exists:device_types,id',
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|string',
            'lastname' => 'required|string',
        ]);
        // Log::info('pass validate');
        DB::beginTransaction();


        $person->update([
            'name' => $validated['name'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'position' => $validated['position'],
            'directorate_id' => $validated['directorate_id'],
            'employment_type_id' => $validated['employee_type_id'],
        ]);

        $internetUser->update([
            'username' => $validated['username'],
            'status' => $validated['status'],
            'device_limit' => $validated['device_limit'],
            'mac_address' => $validated['mac_address'],
            'group_id' => $validated['group_id'],
        ]);

        InternetUserDevice::where('internet_user_id', $internetUser->id)
            ->update([
                'device_type_id' => $validated['device_type_id']
            ]);


        DB::commit();

        return response()->json([
            'message' => 'Internet user successfully updated.',
            'data' => $internetUser,
        ], 200);

        DB::rollBack();

        return response()->json([
            'message' => 'An error occurred while updating the user.',
            'error' => $e->getMessage(),
        ], 500);
    }



    /**
     * Remove thee specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $internetUser = InternetUser::findOrFail($id);


            DB::table('violations')->where('internet_user_id', $internetUser->id)->delete();


            InternetUserDevice::where('internet_user_id', $internetUser->id)->delete();


            $internetUser->delete();


            $person = $internetUser->person;
            if ($person) {
                $person->delete();
            }

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

    public function checkUsername(Request $request)
    {
        $username = $request->input('username');
        $exists = \App\Models\InternetUser::where('username', $username)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function checkEmailInternetUser(Request $request)
    {
        $email = $request->input('email');

        $exists = \App\Models\Person::where('email', $email)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This Email is Already Taken! Please Try Another One!' : ''
        ]);
    }

    public function checkPhoneOfInternetUsers(Request $request)
    {
        $phone = $request->input('phone');

        $exists = \App\Models\Person::where('phone', $phone)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This Phone Number is Registered. Please Try another one!' : ''
        ]);
    }

    public function checkMacAddress(Request $request)
    {
        $mac = $request->input('mac_address');

        $exists = \App\Models\InternetUser::where('mac_address', $mac)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This MAC Address is Already Registered! Please Try Another One!' : ''
        ]);
    }

    public function getDeactivatedUsernames(Request $request)
    {
        $usernames = InternetUser::where('status', 0)
            ->where('username', 'like', '%' . $request->input('query') . '%')
            ->select('username')
            ->get();

        return response()->json([
            'data' => $usernames,
        ]);
    }
}
