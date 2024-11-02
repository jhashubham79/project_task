<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{



    public function index()
    {
      
        return response()->json(Role::all());
    }
    

   
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|regex:/^[6-9]\d{9}$/',
            'description' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Save user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->description = $request->description;
        $user->role_id = $request->role_id;
    
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('public/profile_images');
            $user->profile_image = basename($path);
        }
    
        $user->save();
    
        // Return the user data to update the front-end table
        return response()->json(['user' => $user->load('role')]);
    }

}
