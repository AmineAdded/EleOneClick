<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SignUp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class SendEmailSignUp extends Controller
{
        public function sendEmail(Request $request)
        {
                Mail::to($request->email)->send(new SignUp($request->name));
                return response()->json(['message'=>"Bienvenue"],200);
        }
}
