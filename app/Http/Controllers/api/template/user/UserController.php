<?php

namespace App\Http\Controllers\api\template\user;

use App\Enum\RoleEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    public function store(Request $request){

        $authUser =$request()->user();
        // $authUser = auth()->user();
       

        if($authUser->role_id === RoleEnum::Admin->value){

            return response()->json(
                'Unauthorized User'
            );
        }

    }
}
