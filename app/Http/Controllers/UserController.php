<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Order;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;



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

        if ($user) {
            $user->notify(new VerifyEmailNotification($user));
            auth()->login($user);
            return redirect('/verify-email-page')->with('success', 'User has been registered successfully. Please verify your email.');
        } else {
            return redirect()->back()->withInput()->withErrors(['error' => 'Registration failed. Please try again.']);
        }

        $token = $user->createToken('Russia24')->plainTextToken;
        return response()->json([
            'success' => "Account created successfully",
            'user_id' => $user->id,
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function verify($id, Request $request) {
        if (!$request->hasValidSignature()) {
            return abort(404);
        }

        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
            return redirect('/home')->with('success', 'Email verified successfully');
        } else {
            return redirect('/')->with('error', 'Email already verified');
        }
    }

    public function resend(){
        if(auth()->user()->hasVerifiedEmail()){
            return redirect()->back()->with('error', 'Email already verified');
        }

        auth()->user()->sendEmailVerificationNotification();
        return redirect()->back()->with('success', 'Verification link has been resent to your email');
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

    public function passwordReset(Request $request){
        return view('auth.passwords.reset', [
            'token' => $request->token
        ]);
    }

    public function passwordEmail(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $response = Password::sendResetLink(
        $request->only('email')
    );

    if ($response == Password::RESET_LINK_SENT) {
        return response()->json(['message' => 'Password reset link sent to your email!'], 200);
    } else {
        return response()->json(['message' => 'Failed to send password reset link'], 500);
    }
}

public function passwordUpdate(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string|confirmed|min:8',
        'token' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->password = bcrypt($password);
            $user->save();
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        return response()->json(['message' => 'Password reset was successful'], 200);
    } else {
        return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
    }
}

public function logout(Request $request)
{
    auth()->logout();

    return response()->json(['message' => 'Logout Successfully'], 200);
}


}
