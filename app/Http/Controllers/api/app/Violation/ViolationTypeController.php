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
        $violationsTypes = DB::table('violations_types')
        ->select('id', 'name', 'created_at', ) 
        ->orderBy('id', 'asc')->get();
       

    return response()->json([
        'message' => 'Violation types retrieved successfully',
        'data' => $violationsTypes
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
    public function store(Request $request)
    {
        
   $validatedData = $request->validate([
        'name' => 'required|string|unique:violations_types,name',
    ]);

    
    $violationType = ViolationsType::create([
        'name' => $validatedData['name']
    ]);

    return response()->json([
        'message' => 'Violation Type created successfully',
        'data' => $violationType
    ], 201);
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
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       $validatedData = $request->validate([
            'name' => 'required|string|unique:violations_types,name,' . $id,
        ]);

       
        $violationType = ViolationsType::find($id);

        if (!$violationType) {
            return response()->json([
                'message' => 'Violation Type not found'
            ], 404);
        }

        $violationType->name = $validatedData['name'];
        $violationType->save();

        return response()->json([
            'message' => 'Violation Type updated successfully',
            'data' => $violationType
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       $violationType = ViolationsType::find($id);

        if (!$violationType) {
            return response()->json([
                'message' => 'Violation Type not found'
            ], 404);
        }

        $violationType->delete();

        return response()->json([
            'message' => 'Violation Type deleted successfully'
        ], 200);
    }
}
