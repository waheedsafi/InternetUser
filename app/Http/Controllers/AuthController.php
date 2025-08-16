<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function index()
    {
        $data = DB::table('users as us')
            ->join('roles as ro', 'us.role_id', '=', 'ro.id')
            ->select('us.id', 'us.name', 'us.email', 'ro.name as role_name')
            ->get();

        return response()->json($data);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('YourAppName')->plainTextToken;
        $user->load('role');

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name
            ],
            'message' => 'Login successful',
        ]);
    }

   public function profile(Request $request)
{
    $user = auth()->user();

    // بررسی اینکه آیا کاربر در حال تلاش برای مشاهده پروفایل خودش است یا نه
    if ($request->route('id') != $user->id) {
        return response()->json([
            'success' => false,
            'message' => 'You can only view your own profile.',
        ], 403);
    }

    $user->load('role');

    return response()->json([
        'success' => true,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->name,
            ] : null,
        ],
    ]);
}

  public function updateProfile(Request $request)
{
    $user = auth()->user();

    // بررسی اینکه آیا کاربر در حال تلاش برای به‌روزرسانی پروفایل خودش است یا نه
    if ($request->route('id') != $user->id) {
        return response()->json([
            'success' => false,
            'message' => 'You can only update your own profile.',
        ], 403);
    }

    // ادامه کدهای موجود برای به‌روزرسانی پروفایل
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 400);
    }

    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();
    $user->load('role');

    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->name,
            ] : null,
        ],
    ]);
}

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }


        $roleId = $request->input('role_id');


        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleId,
        ]);


        $token = $newUser->createToken('YourAppName')->plainTextToken;
        $newUser->load('role');


        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $newUser->id,
                'name' => $newUser->name,
                'email' => $newUser->email,
                'role' => $newUser->role ? [
                    'id' => $newUser->role->id,
                    'name' => $newUser->role->name,
                ] : null,
            ],
            'message' => 'User created successfully',
        ]);
    }



    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }

    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        $exists = \App\Models\User::where('email', $email)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    public function systemUsersUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|integer',
            'password' => 'nullable|min:6',
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->role_id = $validatedData['role_id'];

        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();
        $user->load('role');

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'User updated successfully',
        ]);
    }

    public function systemUsersDelete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
