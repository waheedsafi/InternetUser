<?php

namespace App\Http\Controllers\api\app\Violation;

use App\Http\Controllers\Controller;
use App\Models\Violation;
use App\Models\ViolationsType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ViolationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $violations = DB::table('violations')
        ->join('violations_types', 'violations.violation_type_id', '=', 'violations_types.id')  
        ->join('internet_users', 'violations.internet_user_id', '=', 'internet_users.id')  
        ->select('violations.*', 'violations_types.name as violation_type_name', 'internet_users.name as user_name')  
        ->paginate(10);  

    return response()->json([
        'message' => 'Violations retrieved successfully',
        'data' => $violations
    ], 200);
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
    public function store(Request $request ,$id)
    {
        
    $validatedData = $request->validate([
        'internet_user_id' => 'required|exists:internet_users,id',
        'violation_type_id' => 'nullable|exists:violations_types,id',
        'name' => 'nullable|string|unique:violations_types,name',
        'comment' => 'nullable|string',
    ]);

    
    $violation = Violation::findOrFail($id);

   
    if (isset($validatedData['name'])) {
        $violationType = ViolationsType::create([
            'name' => $validatedData['name']
        ]);
        $violation_type_id = $violationType->id;
    } else {
       
        $violation_type_id = $validatedData['violation_type_id'] ?? $violation->violation_type_id;
    }

   
    $violation->update([
        'internet_user_id' => $validatedData['internet_user_id'],
        'violation_type_id' => $violation_type_id,
        'comment' => $validatedData['comment'] ?? $violation->comment, 
    ]);

    
    return response()->json([
        'message' => 'Violation updated successfully',
        'data' => [
            'id' => $violation->id,
            'internet_user_id' => $violation->internet_user_id,
            'violation_type_name' => $violation->violationType->name, 
            'user_name' => $violation->internetUser->name, 
            'comment' => $violation->comment
        ]
    ], 200);
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
         $violation = Violation::with('violationType', 'internetUser') 
        ->findOrFail($id); 

    return response()->json([
        'message' => 'Violation details retrieved successfully',
        'data' => $violation
    ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
          $validatedData = $request->validate([
        'internet_user_id' => 'required|exists:internet_users,id',
        'violation_type_id' => 'nullable|exists:violations_types,id',
        'name' => 'nullable|string|unique:violations_types,name',
        'comment' => 'nullable|string',
    ]);

 
    $violation = Violation::findOrFail($id); 

    
    if (isset($validatedData['name'])) {
        
        $violationType = ViolationsType::create([
            'name' => $validatedData['name']
        ]);
        $violation_type_id = $violationType->id;
    } else {
        $violation_type_id = $validatedData['violation_type_id'] ?? $violation->violation_type_id;
    }

    
    $violation->update([
        'internet_user_id' => $validatedData['internet_user_id'],
        'violation_type_id' => $violation_type_id,
        'comment' => $validatedData['comment'] ?? $violation->comment, 
    ]);

    return response()->json([
        'message' => 'Violation updated successfully',
        'data' => $violation
    ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $violation = Violation::findOrFail($id); 

    
    $violation->delete();

    return response()->json([
        'message' => 'Violation deleted successfully'
    ], 200);
    }
}
