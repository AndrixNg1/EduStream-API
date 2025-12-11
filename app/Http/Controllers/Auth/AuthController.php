<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // event of email verification (if MustVerifyEmail implemented)
        event(new Registered($user));

        // create token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user->only(['id','name','email','email_verified_at']),
            'token' => $token,
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        /**
         *
         * if (method_exists($user, 'hasVerifiedEmail') && ! $user->hasVerifiedEmail()) {
         *   return response()->json([
         *       'message' => 'Email not verified'
         *   ], Response::HTTP_FORBIDDEN);
        * }
         *
         *
         */
        // Option: check email verified


        // create token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user->only(['id','name','email','email_verified_at']),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // revoke current token
        $token = $request->user()?->currentAccessToken();
        if ($token) {
            $token->delete();
        } else {
            // fallback: revoke all
            $request->user()?->tokens()->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
