<?php

namespace App\Http\Controllers\api\app\Directorate;

use App\Http\Controllers\Controller;
use App\Models\Directorate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DirectorateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $dire = DB::table('directorates')->select('id', 
      'name',
      'directorate_type_id'
      ,'directorate_id')->get();

      return response()->json($dire);

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
