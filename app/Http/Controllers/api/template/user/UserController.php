<?php

namespace App\Http\Controllers\api\template\user;

use App\Enum\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //

    public function store(Request $request)
    {

        $authUser = $request()->user();
        // $authUser = auth()->user();


        if ($authUser->role_id === RoleEnum::Admin->value) {

            return response()->json(
                'Unauthorized User'
            );
        }
    }
    public function register(Request $request)
    {
        $user = Auth()->user();
        if (!$user || $user->role_id !== RoleEnum::Admin->value) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);


        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => RoleEnum::User->value,
        ]);




        return response()->json([
            'success' => true,
            'user' => [
                'id' => $newUser->id,
                'name' => $newUser->name,
                'email' => $newUser->email,
                'role' => RoleEnum::User->name,
            ],
            'message' => 'User created successfully',
        ]);
    }
}
