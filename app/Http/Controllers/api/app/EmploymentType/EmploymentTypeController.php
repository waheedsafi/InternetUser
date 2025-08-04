<?php

namespace App\Http\Controllers\api\app\EmploymentType;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmploymentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $emp =  DB::table('employment_types')->select('id','name')->get();

      return response()->json($emp);
    }

    // funiction for report 
    public function employmentTypeCounts()
{
    $counts = DB::table('internet_users')
        ->join('employment_types', 'internet_users.employment_type_id', '=', 'employment_types.id')
        ->select('employment_types.name', DB::raw('COUNT(internet_users.id) as count'))
        ->groupBy('employment_types.name')
        ->get();

    
    return response()->json([
        'employment_type_report' => $counts,
    ]);
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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
