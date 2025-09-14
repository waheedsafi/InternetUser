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

   public function countsByType()
{
    $rows = DB::table('internet_users as iu')
        ->join('groups as g', 'g.id', '=', 'iu.group_id')
        ->select(
            'g.name as group_type',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('g.name')
        ->orderByDesc('total')
        ->get();

    return response()->json($rows, 200);
}

}
