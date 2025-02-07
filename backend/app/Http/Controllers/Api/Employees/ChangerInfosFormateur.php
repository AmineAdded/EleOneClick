<?php

namespace App\Http\Controllers\Api\Employees;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ChangerInfosFormateur extends Controller
{

public function changerInfosFormateur(Request $request, $id)
{
    $user = User::findOrFail($id);
    $image = null;
    if ($request->hasFile('image')) {
        $request->validate(['image'=>"nullable|mimes:jpg,jpeg,png,svg,gif"]);
        $image = $request->file('image')->store('users', 'public');
    }
    $user->name = $request->name;
    $user->lastname = $request->lastname;
    $user->email = $request->email;
    $user->image = $image;
    $user->save();
    

    return response()->json(['data' => $user, 'success' => 'true'], 200);
}

}
