<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;


class ResetPassword extends Controller
{
    public function reset_password(Request $request){
        $validatedData = $request->validate([
            'password_token'=>"required|exists:users",
            'email'=>"required|email|exists:users",
            'password'=>"required"
        ]);
        if($request->isAdmin === 0){
            $notification=Notification::create([
                'message'=>'Mise à jour du mdps de l\'utilisateur ',
                'typeMessage'=> 2,
                'isRead'=>0,
                'user_id'=>$request->id
            ]);
        }
        // dd($request->user->isAdmin);
        $checkCode=User::where('password_token',$request->password_token)->where('email',$request->email)->first();
        if(!$checkCode){
            return response(['message' => 'E-mail ou code incorrect','code'=>$request->password_token], 404);
        }
        if($checkCode->password_token_send_at > now()->addHour()){
             $checkCode->password_token=Null;
             $checkCode->password_token_send_at=Null;
             $checkCode->save();
             return response(['message' => 'le code du mot de passe a expiré'], 422);
        }

        $checkCode->password=bcrypt($request->password);
        $checkCode->password_token=Null;
        $checkCode->password_token_send_at=Null;
        $checkCode->save();

        
        return response(['message' =>'votre mot de passe a été changé avec succé'], 200);
    }

    public function exist_code($code){
        $user = User::where('password_token', $code)->first();
    
        if($user) {
            return response()->json([
                'success' => true,
                'message' => 'Code valide', 
                'data' => []
            ], 200);
        } else {
            
            return response()->json([
                'success' => false,
                'message' => 'Code invalide',
                'data' => []
            ]);
        }
    }
    
}
