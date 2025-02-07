<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\ChangerPassword;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;

class ChangePassword extends Controller
{
    public function ChangerPassword(Request $request){
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
    
        $token= mt_rand(100000, 999999);
        $user_changer_password=User::where('email','=',$request->email)->firstOrFail();
        $user_changer_password->password_token=$token;
        $user_changer_password->password_token_send_at=now();
        $user_changer_password->save();
        Mail::to($request->email)->send(new ChangerPassword($token));
        return response()->json([
            'message' => "Code envoyé à votre email avec succès",
            'sucess' => true,
            'code' => $token,
        ], 200);
    }
}
