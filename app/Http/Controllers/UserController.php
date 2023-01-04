<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Hash;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|numeric|unique:users',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        if($request->file('photo')){
            $photo_name = 'user-'.$request->phone_number.'.'.$request->file('photo')->extension();
            $photo = $request->file('photo');
            $path = public_path('user_photo/');
            $photo->move($path, $photo_name);
        }else{
            $photo_name = null;
        }
        $user_account = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'photo' => $photo_name,
        ]);

        return new UserResource(true, 'Register Account Successfull !', $user_account);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $user_account = User::where('email', $request->email)->first();
        if(!$user_account){
            return new UserResource(false, 'Account Not Found, Please Register Before!', null);
        }elseif(!Hash::check($request->password, $user_account->password)){
            return new UserResource(false, 'Wrong Password! Try Again!', null);
        }

        $token = $user_account->createToken('token')->plainTextToken;
        $data = collect([
            'user_account' => $user_account,
            'token' => $token,
        ]);
        return new UserResource(true, 'Login Successfull!', $data);
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => "Logout Successfull",
        ], 200);
    }
}
