<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getProfile()
    {
        $data['user'] = auth()->user()->load('role');

        return response()->json([
            'response_code' => 200,
            'response_message' => 'Profile showed',
            'data' => $data
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'photo_profile' => 'mimes:jpg,jpeg,png'
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $id = auth()->user()->id;
        $user = User::find($id);

        if ($request->hasFile('photo_profile')) {
            $image = $request->file('photo_profile');
            $image_extension = $image->extension();
            $image_name = time() . '.' . $image_extension;
            $image_folder = '/img/user/';
            $image_location = $image_folder . $image_name;
            $request->photo_profile->move(public_path($image_folder), $image_name);

            if ($user->photo_profile != null) {
                // delete photo_profile after update
                unlink(public_path($user->photo_profile));
            }

            $user->update([
                'name' => $request['name'],
                'photo_profile' => $image_location,
            ]);
        } else {
            $user->update([
                'name' => $request['name'],
            ]);
        }


        return response()->json([
            'response_code' => 201,
            'response_message' => 'User updated',
            'data' => $user
        ], 201);
    }
}
