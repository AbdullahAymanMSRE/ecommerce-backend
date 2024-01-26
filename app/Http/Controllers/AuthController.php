<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\AuthForgotPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\Cart;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->phone_number = $request->phoneNumber;
        $user->password = Hash::make($request['password']);
        $user->remember_token = Str::random(10);
        $user->save();

        Cart::userCartOrCreate($user->id);
        $token = $user->createToken('Laravel Password Grant Client')->plainTextToken;
        $user = User::with('cart.products')->find($user->id);
        $response = ['token' => $token, 'user' => new UserResource($user)];
        return response($response, 200);
    }

    public function login(LoginRequest $request)
    {
        $user = null;
        if (isset($request->email))
            $user = User::where('email', $request->email)->first();
        else
            $user = User::where('username', $request->username)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Password Grant Client')->plainTextToken;
                $user = User::with('cart.products')->find($user->id);
                $response = ['token' => $token, 'user' => new UserResource($user)];
                return response($response, 200);
            }
        }

        $response = ["message" => 'wrong ' . (isset($request->email) ? 'email' : 'username') . ' or password'];
        return response($response, 422);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function forgot_password(ForgotPasswordRequest $request)
    {
        try {
            $response = Password::sendResetLink($request->only('email'));
            switch ($response) {
                case Password::RESET_LINK_SENT:
                    return response()->json(["message" => trans($response)]);
                case Password::INVALID_USER:
                    return response()->json(["message" => trans($response)], 400);
            }
        } catch (Exception $ex) {
            $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
            return response()->json(['message' => $arr['message']], $arr['status']);
        }
        return response()->json();
    }

    public function change_password(Request $request)
    {
        $input = $request->all();
        $userid = $request->user()->id;
        try {
            if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {
                $arr = array("status" => 400, "message" => "Check your old password.", "data" => array());
            } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
                $arr = array("status" => 400, "message" => "Please enter a new password", "data" => array());
            } else {
                User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                $arr = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
            }
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $arr = array("status" => 400, "message" => $msg, "data" => array());
        }
        return response()->json($arr);
    }

    public function user_data(Request $request)
    {
        $user = User::with('cart.products')->find($request->user()->id);
        return new UserResource($user);
    }
}
