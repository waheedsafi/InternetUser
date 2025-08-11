<?php

namespace App\Http\Controllers\api\app\Violation;

use App\Http\Controllers\Controller;
use App\Models\InternetUser;
use App\Models\Violation;
use Illuminate\Http\Request;
use App\Http\Requests\StoreViolationRequest;  // Using custom request for validation
use Illuminate\Support\Facades\DB;

class ViolationController extends Controller
{
    /**
     * Display a listing of violations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $violations = Violation::with(['internetUser', 'violationType'])->get();

        return response()->json([
            'data' => $violations,
        ], 200);
    }

   
    public function store(Request $request)
{
    $validated = $request->validate([
        'internet_user_id' => 'required|exists:internet_users,id',
        'violation_type_id' => 'required|exists:violations_types,id',
        'comment' => 'nullable|string',
    ]);

   
    $violationCount = Violation::where('internet_user_id', $validated['internet_user_id'])->count();

    if ($violationCount >= 2) {
        return response()->json([
            'message' => 'This user has already violated twice and cannot have a new violation.',
        ], 403);
    }

    
    DB::beginTransaction();

    try {
        
        $violation = Violation::create([
            'internet_user_id' => $validated['internet_user_id'],
            'violation_type_id' => $validated['violation_type_id'],
            'comment' => $validated['comment'] ?? '',
        ]);

       
        if ($violationCount + 1 >= 2) {
            $internetUser = InternetUser::find($validated['internet_user_id']);

            if ($internetUser) {
                $internetUser->status = false;
                $internetUser->save();
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'Violation successfully recorded.',
            'data' => $violation,
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack(); 

        return response()->json([
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}
}
