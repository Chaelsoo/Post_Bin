<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'max:255', "unique:users"],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'max:12', 'min:3'],
        ]);

        // If validation fails, return the error messages
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->messages(),
            ], 200);
        }

        // Retrieve the validated input
        $validated = $validator->validated();

        // Create a new user
        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);



        // Return a successful response
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => $user,
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], 201);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'max:12', 'min:3'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->messages(),
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }


        // $token = $user->createToken('auth_token')->plainTextToken;

        // Auth::login($user);
        // return redirect()->route("index");
        return response()->json([
            'status' => 'success',
            // 'token' => $token,
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect("/");
    }
}
