<?php

namespace App\Http\Controllers\api\app\Account;

use App\Http\Controllers\Controller;
use App\Models\AccountActivation;
use App\Models\InternetUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountActivationController extends Controller
{

    // public function index(){
    //     $activate = DB::table(); 
    // }
    public function activateAccount(Request $request)
    {
        $request->validate([
            'internet_user_id' => 'required|exists:internet_users,id',
            'reason' => 'required|string'
        ]);
        $user = InternetUser::find($request->internet_user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->status = true;
        $user->save();
        AccountActivation::create([
            'internet_user_id' => $user->id,
            'reason' => $request->reason,

        ]);
        return response()->json(['message' => 'Account activated successfully']);
    }
}
