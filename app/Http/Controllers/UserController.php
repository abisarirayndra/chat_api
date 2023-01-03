<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Hash;
use App\Http\Resources\ChatResource;

class UserController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|numeric|unique:users',
            'password' => 'required',
            'photo' => 'required|image|mimes:jpg,png,jpeg,webp|max:1024'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $photo_name = 'user-'.$request->phone_number.'.'.$request->file('photo')->extension();
        $photo = $request->file('photo');
        $path = public_path('user_photo/');
        $photo->move($path, $photo_name);

        $user_account = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'photo' => $photo_name,
        ]);

        return new ChatResource(true, 'Register Account Successfull !', $user_account);
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
            return new ChatResource(false, 'Account Not Found, Please Register Before!', null);
        }elseif(!Hash::check($request->password, $user_account->password)){
            return new ChatResource(false, 'Wrong Password! Try Again!', null);
        }

        $token = $user_account->createToken('token')->plainTextToken;
        $data = collect([
            'user_account' => $user_account,
            'token' => $token,
        ]);
        return new ChatResource(true, 'Login Successfull!', $data);
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => "Logout Successfull",
        ], 200);
    }
}
