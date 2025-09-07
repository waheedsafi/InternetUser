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
        $data = DB::table('internet_users as intu')
            ->join('persons as per', 'per.id', '=', 'intu.person_id')
            ->join('directorates as dir', 'dir.id', '=', 'per.directorate_id')
            ->join('groups as gr', 'gr.id', '=', 'intu.group_id')
            ->join('employment_types as emp', 'emp.id', '=', 'per.employment_type_id')

            ->select(
                'per.name',
                'emp.name as employment_type',
                'per.email',
                'per.phone',
                'per.lastname',
                'intu.username',
                'dir.name as directorate',
                'per.position',
                'gr.name as groups',
                'intu.status',
                'intu.id',

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
            //ibrahimi-it changed this: accept devices[] payload in addition to legacy fields
            $validated = $request->validate([
                'username' => 'required|string|unique:internet_users,username',
                'status' => 'required|in:0,1',
                'phone' => 'required|string|max:15|unique:persons,phone',
                'directorate_id' => 'required|exists:directorates,id',
                'email' => 'required|unique:persons,email',
                'employee_type_id' => 'required|exists:employment_types,id',
                // Legacy map
                'device_macs' => 'nullable|array',
                'device_macs.*' => 'nullable|string',
                //ibrahimi-it changed this: new per-device payload (either this OR device_type_ids[])
                'devices' => 'nullable|array',
                'devices.*.device_type_id' => 'required_with:devices|exists:device_types,id',
                'devices.*.mac_address' => 'nullable|string',
                'group_id' => 'required|exists:groups,id',
                'position' => 'required|string',
                'device_type_ids' => 'required_without:devices|array',
                'device_type_ids.*' => 'exists:device_types,id',
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
                // 'mac_address' => $validated['mac_address'],
            ]);

            //ibrahimi-it changed this: prefer explicit devices[] if present; fallback to legacy mapping
            $devicesPayload = $validated['devices'] ?? null;
            if (is_array($devicesPayload) && count($devicesPayload) > 0) {
                foreach ($devicesPayload as $dev) {
                    $mac = $dev['mac_address'] ?? null;
                    $mac = is_string($mac) ? strtoupper(trim($mac)) : null;
                    $mac = ($mac === '' ? null : $mac);
                    InternetUserDevice::create([
                        'internet_user_id' => $internetUser->id,
                        'device_type_id' => (int)$dev['device_type_id'],
                        'mac_address' => $mac,
                    ]);
                }
            } else {
                // Normalize device_macs keys to integers for correct mapping by device_type_id
                $normalizedMacs = [];
                $macs = $validated['device_macs'] ?? [];
                foreach ($macs as $k => $v) {
                    $normalizedMacs[(int)$k] = $v;
                }
                // sanitize macs: trim, uppercase, empty string -> null
                $normalizedMacsClean = [];
                foreach ($normalizedMacs as $k => $v) {
                    $val = is_string($v) ? strtoupper(trim($v)) : null;
                    $normalizedMacsClean[(int)$k] = ($val === '' ? null : $val);
                }
                foreach ($validated['device_type_ids'] as $deviceTypeId) {
                    InternetUserDevice::create([
                        'internet_user_id' => $internetUser->id,
                        'device_type_id' => $deviceTypeId,
                        'mac_address' => $normalizedMacsClean[(int)$deviceTypeId] ?? null,
                    ]);
                }
            }

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
        $internetUser = DB::table('internet_users as intu')
            ->join('persons as per', 'per.id', '=', 'intu.person_id')
            ->join('directorates as dir', 'dir.id', '=', 'per.directorate_id')
            ->join('employment_types as emp', 'emp.id', '=', 'per.employment_type_id')
            ->leftJoin('internet_user_devices as user', 'user.internet_user_id', '=', 'intu.id')
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
            ->where('intu.id', '=', $id)
            ->select(
                'intu.id',
                // DB::raw('GROUP_CONCAT(DISTINCT dt.name ORDER BY dt.name) as device_types'),
                'dt.name as device_type',
                // select per-row mac as alias to build device_macs without exposing a top-level mac_address
                'user.mac_address as device_mac',
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
                'gr.name as groups',
                'val.comment',
                'valt.name as violation_type',
                // fardin added this line
                'user.device_type_id',
                DB::raw('(SELECT COUNT(*) FROM violations WHERE internet_user_id = intu.id) as violation_count'),
                'parent_dir.name as deputy'
            )
            ->get();
        ///


        if (!$internetUser || $internetUser->isEmpty() || !$internetUser[0]) {
            return response()->json([
                'message' => 'Internet user not found.',
            ], 404);
        }

        // fardin changed: collect mac addresses per device type (include null/empty too)
        // fardin also cast keys to string so JSON returns an object map, not a numeric array
        $deviceMacs = [];
        $internetUser->map(function ($item) use (&$deviceMacs) {
            $deviceMacs[(string)$item->device_type_id] = $item->device_mac; // fardin use alias from select
        });
        $user = $internetUser[0];
        // We now use device_macs per device type; avoid misleading single mac_address (ensure it's not present)
        if (isset($user->mac_address)) unset($user->mac_address);
        // or: $user->mac_address = null;
        // fardin added this line
        $user->device_macs = $deviceMacs;

        // ADD THESE TWO LINES:
        $user->device_type = $internetUser->pluck('device_type')->toArray();
        $user->device_type_id = $internetUser->pluck('device_type_id')->toArray();

        //ibrahimi-it changed this: also return a per-row devices[] list for multiple same-type devices
        $devices = [];
        foreach ($internetUser as $row) {
            $devices[] = [
                'device_type_id' => (int)$row->device_type_id,
                'mac_address' => $row->device_mac,
            ];
        }
        $user->devices = $devices;

        return response()->json([
            'message' => 'Internet user found.',
            'data' => $user,
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $internetUser = InternetUser::findOrFail($id);
        $person = $internetUser->person;
        // $internetUserDevice = InternetUserDevice::where('internet_user_id', $internetUser->id)->first();


        //ibrahimi-it changed this: accept devices[] payload in addition to legacy fields
        $validated = $request->validate([
            'username' => 'required|string|unique:internet_users,username,' . $internetUser->id,
            'status' => 'required|in:0,1',
            'phone' => 'required|string|max:15|unique:persons,phone,' . $person->id,
            'directorate_id' => 'required|exists:directorates,id',
            'email' => 'required|email|unique:persons,email,' . $person->id,
            'employee_type_id' => 'required|exists:employment_types,id',
            'position' => 'required|string',
            'device_limit' => 'required|integer',
            // fardin changed this line from ... to add device_macs validation
            'device_macs' => 'nullable|array',
            'device_macs.*' => 'nullable|string',
            'device_type_ids' => 'required_without:devices|array',
            'device_type_ids.*' => 'exists:device_types,id',
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|string',
            'lastname' => 'required|string',
            //ibrahimi-it changed this: new per-device payload
            'devices' => 'nullable|array',
            'devices.*.device_type_id' => 'required_with:devices|exists:device_types,id',
            'devices.*.mac_address' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

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
                // fardin changed this line: removed 'mac_address' from update to fix undefined array key error
                'group_id' => $validated['group_id'],
            ]);


            InternetUserDevice::where('internet_user_id', $internetUser->id)->delete();

            //ibrahimi-it changed this: prefer devices[] if provided; else fallback to legacy map
            $devicesPayload = $validated['devices'] ?? null;
            if (is_array($devicesPayload) && count($devicesPayload) > 0) {
                foreach ($devicesPayload as $dev) {
                    $mac = $dev['mac_address'] ?? null;
                    $mac = is_string($mac) ? strtoupper(trim($mac)) : null;
                    $mac = ($mac === '' ? null : $mac);
                    InternetUserDevice::create([
                        'internet_user_id' => $internetUser->id,
                        'device_type_id' => (int)$dev['device_type_id'],
                        'mac_address' => $mac,
                    ]);
                }
            } else {
                // normalize and sanitize device_macs before recreate
                $macs = $validated['device_macs'] ?? [];
                $normalizedMacs = [];
                foreach ($macs as $k => $v) {
                    $normalizedMacs[(int)$k] = $v;
                }
                $normalizedMacsClean = [];
                foreach ($normalizedMacs as $k => $v) {
                    $val = is_string($v) ? strtoupper(trim($v)) : null;
                    $normalizedMacsClean[(int)$k] = ($val === '' ? null : $val);
                }
                foreach ($validated['device_type_ids'] as $deviceTypeId) {
                    InternetUserDevice::create([
                        'internet_user_id' => $internetUser->id,
                        'device_type_id' => $deviceTypeId,
                        // fardin use sanitized map
                        'mac_address' => $normalizedMacsClean[(int)$deviceTypeId] ?? null,
                    ]);
                }
            }

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

        $exists = \App\Models\InternetUserDevice::where('mac_address', $mac)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This MAC Address is Already Registered! Please Try Another One!' : ''
        ]);
    }

    public function getDeactivatedUsernames(Request $request)
    {
        $usernames = InternetUser::where('status', 0)
            ->where('username', 'like', '%' . $request->input('query') . '%')
            ->select('id', 'username')
            ->get();

        return response()->json([
            'data' => $usernames,
        ]);
    }

    public function Violationform()
    {
        $internetUser = DB::table('internet_users as intu')
            ->join('persons as per', 'per.id', '=', 'intu.person_id')
            ->join('directorates as dir', 'dir.id', '=', 'per.directorate_id')
            ->leftJoin('directorates as parent_dir', 'parent_dir.id', '=', 'dir.directorate_id')
            ->leftJoin('violations as val', function ($join) {
                $join->on('val.internet_user_id', '=', 'intu.id')
                    ->whereRaw('val.id = (
                    SELECT MAX(v2.id) 
                    FROM violations v2 
                    WHERE v2.internet_user_id = intu.id
                )');
            })
            ->select(
                'intu.id',
                'per.name',
                'intu.username',
                'dir.name as directorate',
                'per.position',
                'val.comment',
                DB::raw('(SELECT COUNT(*) FROM violations WHERE internet_user_id = intu.id) as violation_count'),
                'parent_dir.name as deputy'
            )
            ->get();
        if (!$internetUser) {
            return response()->json([
                'message' => 'Violations not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Violations not found.',
            'data' => $internetUser
        ], 200);
    }


    public function individualReport(Request $request)
    {
        $username = $request->query('username');
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        $internetUser = InternetUser::where('username', $username)->first();

        if (!$internetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $person = $internetUser->person;

        if (!$person) {
            return response()->json(['message' => 'Person data missing for this user'], 404);
        }

        $personData = DB::table('persons as p')
            ->leftJoin('directorates as d', 'd.id', '=', 'p.directorate_id')
            ->leftJoin('directorates as parent', 'parent.id', '=', 'd.directorate_id')
            ->where('p.id', $person->id)
            ->select(
                'p.name',
                'p.lastname',
                'd.name as directorate',
                'parent.name as deputyMinistry'
            )
            ->first();

        $violationCountQuery = $internetUser->violations();

        if ($startDate) {
            $violationCountQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $violationCountQuery->whereDate('created_at', '<=', $endDate);
        }

        $violationCount = $violationCountQuery->count();

        $trendQuery = $internetUser->violations();

        if ($startDate) {
            $trendQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $trendQuery->whereDate('created_at', '<=', $endDate);
        }

        $trend = $trendQuery
            ->selectRaw('DATE(created_at) as date, COUNT(*) as violations')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'data' => [
                'name' => $personData->name ?? $person->name,
                'lastname' => $personData->lastname ?? $person->lastname,
                'directorate' => $personData->directorate ?? 'N/A',
                'deputyMinistry' => $personData->deputyMinistry ?? 'N/A',
                'violations' => $violationCount,
                'trend' => $trend
            ]
        ]);
    }

    public function generalReport(Request $request)
    {
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        $data = DB::table('internet_users as iu')
            ->join('persons as p', 'p.id', '=', 'iu.person_id')
            ->join('directorates as d', 'd.id', '=', 'p.directorate_id')
            ->leftJoin('violations as v', function ($join) use ($startDate, $endDate) {
                $join->on('v.internet_user_id', '=', 'iu.id');
                if ($startDate) $join->whereDate('v.created_at', '>=', $startDate);
                if ($endDate) $join->whereDate('v.created_at', '<=', $endDate);
            })
            ->select('d.id', 'd.name as directorate', DB::raw('COUNT(v.id) as violations'))
            ->groupBy('d.id', 'd.name')
            ->havingRaw('COUNT(v.id) > 0')
            ->get();

        return response()->json($data);
    }
}
