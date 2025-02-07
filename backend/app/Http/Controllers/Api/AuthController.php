<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use App\Mail\SignUp;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
        
            $token = $user->createToken('api_token')->plainTextToken;
            $respnose = [
                'token' => $token,
                'user' => $user,
                'isAdmin' => $user->isAdmin
            ];
            if($user->isAdmin === 0){
                $notification=Notification::create([
                    'message'=>'Connexion de l\'utilisateur ',
                    'typeMessage'=> 0,
                    'isRead'=>0,
                    'user_id'=>$user->id
                ]);
            }

            return response()->json(['data' => $respnose], 200);
        } else {
            return response()->json(['data' => "Utilisateur non trouvé", 'status' => "user"], 401);
        }
    }

    public function logout()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response([
            'success' => true
        ]);
    }

    public function SignUp(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        $image=null;
        $isAdmin = str_ends_with($request->email, '@admin.com');
        if ($request->hasFile('image')) {
            $request->validate(['image'=>"nullable|mimes:jpg,jpeg,png,svg,gif"]);
            $image = $request->file('image')->store('users', 'public');
        }
        $user = User::create([
            'name' => $request->name,
            'lastname'=>$request->lastname,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'isAdmin' => $isAdmin,
            'image'=>$image
        ]);

        if ($isAdmin === false) {
            $notification=Notification::create([
                'message' => 'Inscription de l\'utilisateur ',
                'typeMessage' => 1,
                'isRead' => 0,
                'user_id' => $user->id
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;
        $respnose = [
            'token' => $token,
            'user' => $user,
            'isAdmin' =>number_format($user->isAdmin)
        ];

        Mail::to($request->email)->send(new SignUp($request->name));
    
        return response()->json(['data' => $respnose], 200);
    }
    
}
