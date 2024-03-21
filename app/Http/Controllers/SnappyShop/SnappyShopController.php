<?php

namespace App\Http\Controllers\SnappyShop;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginGoogleRequest;
use App\Http\Requests\SnappyTokenRequest;
use App\Models\SnappyToken;
use App\Models\User;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class SnappyShopController extends Controller
{
    public function loginGoogle(LoginGoogleRequest $request)
    {
        try {
            $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID_OAUTH')]);  // Specify the CLIENT_ID of the app that accesses the backend
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
                    'success' => true,
                    'access_token' => $token,
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

    public function saveSnappyToken(SnappyTokenRequest $request)
    {
        DB::beginTransaction();
        try {
            $user_id = auth()->user()->id;
            SnappyToken::updateOrCreate(
                [
                    'token' => $request->token
                ],
                ['user_id' => $user_id,]
            );

            DB::commit();
            return [
                'success' => true,
                'message' => 'Snappy token registered successfully'
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
