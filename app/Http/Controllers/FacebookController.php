<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class FacebookController extends Controller
{
    public function facebookRedirect()
    {
        return Socialite::with('facebook')->redirect();
    }

    public function loginWithFacebook()
    {
        try {

            $user = Socialite::with('facebook')->stateless()->user();
            $myUser = User::where([
                ['provider_id', $user->id],
                ['provider_name', 'facebook']
            ])->first();

            //! Not Working
            $userWithEmail = User::where([
                ['email', $user->email]
            ])->first();

            if (!$myUser)
                $myUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'provider_id' => $user->id,
                    'provider_name' => 'facebook',
                ]);
            else if (isset($userWithEmail)) {
                $userWithEmail->provider_id = $user->id;
            }

            Cart::userCartOrCreate($myUser->id);
            $token = $myUser->createToken("Facebook")->plainTextToken;
            $myUser = User::with('cart.products')->find($myUser->id);
            return response()->json(['token' => $token, 'user' => new UserResource($myUser)], 201);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
}
