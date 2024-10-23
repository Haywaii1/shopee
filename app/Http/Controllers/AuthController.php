<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
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

}
