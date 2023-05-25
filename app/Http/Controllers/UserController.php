<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:2',
            'password' => 'required|min:5',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $credentials = $request->only('username', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user = Auth::user();

        } catch (JWTException $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }

        return response()->json([compact('user','token')], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|min:2',
            'password' => 'required|min:5',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $check = User::where('username', $request->username)->first();

        if($check){
            return response()->json('User already exist', 409);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'password' => bcrypt($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),200);
    }

    public function userlist(Request $request)
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

			$userlist = User::paginate(10);

			return response()->json(compact('userlist'), 200);

		} catch (JWTException $e) {

            return response()->json(['error' => 'Internal Server Error'], 500);
        }

    }

    public function logout(Request $request)
	{

        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        if($removeToken) {
            return response()->json([
                'success' => true,
                'message' => 'Logout Success!',
            ]);
        }else{
			return response()->json([
                'success' => false,
                'message' => 'Logout Failed!',
            ]);
		}
	}

}
