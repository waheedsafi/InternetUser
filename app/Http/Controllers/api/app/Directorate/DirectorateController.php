<?php

namespace App\Http\Controllers\api\app\Directorate;

use App\Http\Controllers\Controller;
use App\Models\Directorate;
use Illuminate\Http\Request;

class DirectorateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $dir = Directorate::with([
        'directorateType:id,name',
        'parentDirectorate:id,name'
       ])->select('id','name','directorate_type_id','directorate_id')->get();
        return response()->json($dir,201);
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
        $request->validate([
            'name'=>'required|string|max:255',
            'directorate_type_id'=>'required|exists:directorate_types,id',
            'directorate_id'=>'nullable|exists:directorates,id',

        ]);
        $dir = Directorate::create([
            'name'=>$request->name,
            'directorate_type_id'=>$request->directorate_type_id,
            'directorate_id'=>$request->directorate_id
        ]);
        return response()->json($dir,201);
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
