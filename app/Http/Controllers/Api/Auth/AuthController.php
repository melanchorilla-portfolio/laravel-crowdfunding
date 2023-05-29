<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Models\OtpCode;
use App\Events\UserRegistered;
use App\Events\RegeneratedOtpCodeEvent;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    
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

    public function verification(Request $request)
    {
        Validator::make($request->all(), ['otp' => 'required']);

        $otp_code = OtpCode::where('otp', $request->otp)->first();

        if (!$otp_code) {
            return response()->json([
                "response_code" => 400,
                "response_message" => "OTP not found!"
            ], 400);
        }

        $now = Carbon::now();

        if ($now > $otp_code->valid_until) {
            return response()->json([
                "response_code" => 400,
                "response_message" => "OTP expired!"
            ], 400);
        }

        $user = User::find($otp_code->user_id);
        $user->email_verified_at = $now;
        $user->save();

        // delete otp
        $otp_code->delete();

        return response()->json([
            "response_code" => 200,
            "response_message" => "E-mail verified!"
        ], 200);
    }

    public function regenerateOtpCode(Request $request)
    {
        Validator::make($request->all(), ['email' => 'required']);

        $user = User::where('email', $request->email)->first();
    
        $user->generate_otp_code();

        // event & listener
        event(new RegeneratedOtpCodeEvent($user));

        return response()->json([
            "response_code" => 201,
            "response_message" => "OTP Code generated!",
            "data" => $user
        ], 201);
    }

    public function login(Request $request)
    {
        // set validation
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // get credentials from request
        $credentials = $request->only('email', 'password');

        // if auth failed
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'response_code' => 401,
                'response_message' => "Incorrect e-mail or password"
            ], 401);
        }

        //if auth success
        return response()->json([
            'response_code' => 200,
            'response_message' => "Logged in",
            'user'    => auth()->guard('api')->user(),
            'token'   => $token
        ], 200);
    }
    
    public function logout()
    {
        //remove token
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());
        auth()->logout();

        if ($removeToken) {
            //return response JSON
            return response()->json([
                'message' => 'Logout Success!',
            ]);
        }
    }

    public function updatePassword(Request $request)
    {
        $id = auth()->user()->id;

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::find($id);

        $user->update([
            'password' => Hash::make($request['password'])
        ]);

        return response()->json([
            'response_code' => 200,
            'response_message' => 'Password updated successfully',
        ], 201);
    }
}
