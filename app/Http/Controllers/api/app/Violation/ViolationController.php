<?php

namespace App\Http\Controllers\api\app\Violation;

use App\Http\Controllers\Controller;
use App\Models\InternetUser;
use App\Models\Violation;
use Illuminate\Http\Request;
use App\Http\Requests\StoreViolationRequest;  // Using custom request for validation

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

    /**
     * Store a newly created violation in storage.
     *
     * @param  \App\Http\Requests\StoreViolationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            

            $violation = Violation::create([
                'internet_user_id' => $request->internet_user_id,
                'violation_type_id' => $request->violation_type_id,
                'comment' => $request->comment ?? '',
            ]);

            
            $violationCount = Violation::where('internet_user_id', $request->internet_user_id)->count();

            if ($violationCount >=2) {
                $internetUser = InternetUser::find($request->internet_user_id);
                if ($internetUser) {
                    $internetUser->status = false;
                    $internetUser->save();
                }
            }

            return response()->json([
                'message' => 'Violation created successfully!',
                'data' => $violation,
            ], 201);
        } catch (\Exception $e) {
            
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
