<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Order;


class UserController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'address' => 'required',
            'password' => 'required|confirmed|min:8',
        ], [
            'email.unique' => 'The email has already been taken.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $formFields = [
            'first_name'=> $request->first_name,
            'last_name'=> $request->last_name,
            'email'=> $request->email,
            'phone'=> $request->phone,
            'address'=> $request->address,
            'password'=> bcrypt($request->password),
        ];

        $user = User::create($formFields);

        if (!$user) {
            return response()->json(['errors' => "Account creation failed"], 500);
        }

        $token = $user->createToken('Russia24')->plainTextToken;
        return response()->json([
            'success' => "Account created successfully",
            'user_id' => $user->id,
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(["errors" => "Incorrect credentials"], 401); // Unauthorized
        }

        $token = $user->createToken('Russia24')->plainTextToken;
        return response()->json([
            'success' => "Logged in successfully",
            'user_id' => $user->id,
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}
