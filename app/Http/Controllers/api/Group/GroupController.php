<?php

namespace App\Http\Controllers\Api\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function index(){
        $group = DB::table('groups')->select('id','name')->get();
        return response()->json($group);
    }
}
