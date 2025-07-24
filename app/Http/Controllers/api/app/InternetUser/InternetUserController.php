<?php

namespace App\Http\Controllers\api\app\InternetUser;

use App\Models\InternetUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
    ->leftJoin('directorates as parent_dir', 'parent_dir.id', '=', 'dir.directorate_id')  
    ->leftJoin('violations as val', 'val.internet_user_id', '=', 'intu.id')
    ->select(
        'intu.id',
        'per.name',
        'per.lastname',
        'intu.username',
        'per.phone',
        'dir.name as directorate',  
        'intu.status',
        DB::raw('COUNT(val.id) as count'),  
        'parent_dir.name as deputy'  
    )
    ->groupBy(
        'intu.id',
        'per.name',
        'per.lastname',
        'intu.username',
        'per.phone',
        'per.directorate_id',
        'intu.status',
        'dir.name',
        'parent_dir.name'  
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
