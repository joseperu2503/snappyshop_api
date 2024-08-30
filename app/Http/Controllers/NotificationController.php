<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceFcmTokenRequest;
use App\Models\DeviceFcmToken;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class NotificationController extends Controller
{
    public function getFirebaseToken()
    {
        $response = Http::withoutVerifying()->get(env('FIREBASE_TOKEN_URL'));

        return $response;
    }

    public function sendNotifications()
    {
        try {
            $firebase_token = $this->getFirebaseToken();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while getting the token.',
            ], 500);
        }

        $device_fcm_tokens =  DeviceFcmToken::all();
        foreach ($device_fcm_tokens as $device_fcm_token) {
            $this->sendNotification($firebase_token, $device_fcm_token);
        }

        return [
            'success' => true,
            'message' => 'notifications sended successfully',
        ];
    }

    public function sendNotification($firebase_token, DeviceFcmToken $device_fcm_token)
    {
        $url = env('FIREBASE_NOTIFICATION_URL');

        $product = Product::inRandomOrder()->first();
        $data = [
            "message" => [
                "token" => $device_fcm_token->token,
                "data" => [
                    "type" => "product",
                    "productId" => strval($product->id)
                ],
                "notification" => [
                    "title" => $product->name,
                    "body" => '$' . $product->price,
                ],
                "android" => [
                    "notification" => [
                        "image" => !empty($product->images) ? $product->images[0] : null
                    ]
                ]
            ]
        ];

        $response = Http::withToken($firebase_token)->post($url, $data);

        if ($response->successful()) {
            $responseData = $response->json();
            return response()->json($responseData);
        } else {

            $errorResponse = $response->json();

            //si el error regresa estos codigos de error es por que el token ya no es valido
            if (
                $errorResponse['error']['details'][0]['errorCode'] == 'UNREGISTERED' ||
                $errorResponse['error']['details'][0]['errorCode'] == 'INVALID_ARGUMENT '
            ) {
                $device_fcm_token->delete();
            }

            return response()->json([
                'success' => false,
                'error' => $errorResponse['error'],
            ], $response->status());
        }
    }

    public function saveDeviceFcmToken(DeviceFcmTokenRequest $request)
    {
        DB::beginTransaction();
        try {

            $user = auth()->user();

            DeviceFcmToken::updateOrCreate(
                [
                    'token' => $request->token
                ],
                ['user_id' => $user ? $user->user_id : null,]
            );

            DB::commit();
            return [
                'success' => true,
                'message' => 'Device FCM token registered successfully'
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
