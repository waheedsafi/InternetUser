<?php

namespace App\Http\Controllers\api\app\Account;

use App\Http\Controllers\Controller;
use App\Models\AccountActivation;
use App\Models\InternetUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountActivationController extends Controller
{

    public function index()
    {
        $activate = DB::table('account_activations as ac')
            ->join('internet_users as intu', 'intu.id', '=', 'ac.internet_user_id')
            ->select(
                'ac.id',
                'intu.username',
                'ac.reason',
                'ac.created_at'
            )
            ->get();

        return response()->json($activate);
    }


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

    public function update(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string'
        ]);

        $activation = AccountActivation::find($id);

        if (!$activation) {
            return response()->json(['message' => 'Activation record not found'], 404);
        }

        $activation->reason = $request->reason;
        $activation->save();

        return response()->json(['message' => 'Activation record updated successfully']);
    }
    public function destroy($id)
    {
        $activation = AccountActivation::find($id);

        if (!$activation) {
            return response()->json(['message' => 'Activation record not found'], 404);
        }

        $activation->delete();

        return response()->json(['message' => 'Activation record deleted successfully']);
    }
    public function edit($id)
    {
        $activation = DB::table('account_activations as ac')
            ->join('internet_users as iu', 'iu.id', '=', 'ac.internet_user_id')
            ->select(
                'ac.id',
                'ac.internet_user_id',
                'iu.username',
                'ac.reason',
                'ac.created_at'
            )
            ->where('ac.id', $id)
            ->first();

        if (!$activation) {
            return response()->json(['message' => 'Activation record not found'], 404);
        }

        return response()->json($activation);
    }
}
