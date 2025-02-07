<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResetPassword;
use App\Mail\ResetPassword;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class ForgotPassword extends Controller
{
   
public function forgot_password(StoreResetPassword $request)
{
        $token= mt_rand(100000, 999999);
        $user_forgot_password=User::where('email','=',$request->email)->firstOrFail();
        $user_forgot_password->password_token=$token;
        $user_forgot_password->password_token_send_at=now();
        $user_forgot_password->save();
        Mail::to($request->email)->send(new ResetPassword($token));
        return response()->json(['message'=>"Code Envoyer à votre email avec succés",'code'=>$token],200);
}
}
