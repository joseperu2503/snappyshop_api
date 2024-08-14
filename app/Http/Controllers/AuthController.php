<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginGoogleRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Google_Client;
use Illuminate\Http\Request;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect email or password'
            ], 401);
        }

        return response()->json([
            'access_token' => $token,
            'user' => new UserResource(auth()->user())
        ]);
    }

    public function register(RegisterRequest $request)
    {
        User::create($request->all());

        return [
            'success' => true,
            'message' => 'User registered successfully'
        ];
    }

    public function loginGoogle(LoginGoogleRequest $request)
    {
        try {
            $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID_OAUTH')]);
            $payload = $client->verifyIdToken($request->id_token);
            if ($payload) {
                $email = $payload['email'];
                $user = User::where('email', $email)->first();

                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' =>  'Unregistered user',
                    ], 400);
                }

                $token = JWTAuth::fromUser($user);

                return response()->json([
                    'access_token' => $token,
                    'user' => new UserResource($user)
                ]);
            } else {
                // Invalid ID token
                return response()->json([
                    'success' => false,
                    'message' =>  'Invalid ID token',
                ], 400);
            }
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' =>  'Invalid ID token',
            ], 400);
        }
    }
}
