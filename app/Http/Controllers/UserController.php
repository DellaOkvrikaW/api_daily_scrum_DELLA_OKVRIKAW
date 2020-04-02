<?php

namespace App\Http\Controllers;
use App\User;
use JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
	//fungsi untuk login
	public function login(Request $request){
		$credentials = $request->only('email', 'password');
		try {
			if(!$token = JWTAuth::attempt($credentials)){
				return response()->json([
						'logged' 	=>  false,
						'message' 	=> 'Invalid email and password'
					]);
			}
		} catch(JWTException $e){
			return response()->json([
						'logged' 	=> false,
						'message' 	=> 'Generate Token Failed'
					]);
		}
		return response()->json([
					"logged"    => true,
                    "token"     => $token,
                    "message" 	=> 'Login berhasil'
		]);
    }
    
    //fungsi untuk login check
	public function LoginCheck(){
		try {
			if(!$user = JWTAuth::parseToken()->authenticate()){
				return response()->json([
						'auth' 		=> false,
						'message'	=> 'Invalid token'
					]);
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
			return response()->json([
						'auth' 		=> false,
						'message'	=> 'Token expired'
					], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
			return response()->json([
						'auth' 		=> false,
						'message'	=> 'Invalid token'
					], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e){
			return response()->json([
						'auth' 		=> false,
						'message'	=> 'Token absent'
					], $e->getStatusCode());
		}

		 return response()->json([
		 		"auth"      => true,
                "user"    	=> $user
		 ], 201);
	}

    //fungsi untuk logout
	public function logout(Request $request)
    {
        if(JWTAuth::invalidate(JWTAuth::getToken())) {
            return response()->json([
                "logged"    => false,
                "message"   => 'Logout berhasil'
            ], 201);
        } else {
            return response()->json([
                "logged"    => true,
                "message"   => 'Logout gagal'
            ], 201);
        }
    }

    
    //fungsi untuk register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname'         => 'required|string|max:255',
            'lastname'          => 'required|string|max:255',
            'email'             => 'required|string|email|max:100|unique:users',
            'password'          => 'required_with:password_verify|same:password_verify|string|min:6',
            'password_verify'   => 'string|min:6',
        ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson());
            }
        $user = User::create([
            'firstname'         => $request->get('firstname'),
            'lastname'          => $request->get('lastname'),
            'email'             => $request->get('email'),
            'password'          => Hash::make($request->get('password')),
            'password_verify'   => Hash::make($request->get('password_verify')),
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user','token'));
    }

}
