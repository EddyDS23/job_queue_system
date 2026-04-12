<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class AuthController extends Controller
{
    
    public function register(Request $request):JsonResponse{

        $request->validate([
            'name'=>'required|string',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8',
        ]);
        

        $user = User::create(
            $request->only(['name','email','password'])
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user'=>$user,'token'=>$token]);

    }

    public function login(Request $request):JsonResponse{
        
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|string|min:8'
        ]);

        $credentials = $request->only(['email','password']);

        $answer = Auth::attempt($credentials);

        if(!$answer){
            return response()->json(['message'=>'Invalid Credentials'],401);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user'=>$user,'token'=>$token]);

    }

}
