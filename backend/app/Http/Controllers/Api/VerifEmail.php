<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerifEmail extends Controller
{
    public function VerifEmail(Request $request)
    {
        $email = $request->query('email'); // Récupère le paramètre "email" depuis la query string
        $user = User::where('email', $email)->first();
        if ($user) {
            return response()->json([
                'success'   => true,
                'message'   => 'email valide',
                'data'      => []
            ], 200);
        } else {
            return response()->json([
                'success'   => false,
                'message'   => 'email invalide',
                'data'      => []
            ]);
        }
    }
    
}
