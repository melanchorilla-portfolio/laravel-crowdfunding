<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request) 
    {
        // set validation
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $data['user'] = $user;

        $token =  Auth::login($user);
        // success
        if ($user) {
            // event & listener
            event(new UserRegistered($user));

            return response()->json([
                "response_code" => 201,
                "response_message" => "User registered successfully",
                "data" => $data,
                "token" => $token
            ], 201);
        }

        // fail
        return response()->json([
            "response_code" => 409,
            "response_message" => "Error!"
        ], 409);
    }

    public function verification()
    {

    }
}
