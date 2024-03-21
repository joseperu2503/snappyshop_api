<?php

namespace App\Http\Controllers\SnappyShop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SnappyToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

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

        $snappy_tokens =  SnappyToken::all();
        foreach ($snappy_tokens as $snappy_token) {
            $this->sendNotification($firebase_token, $snappy_token);
        }

        return [
            'success' => true,
            'message' => 'notifications sended successfully',
        ];
    }

    public function sendNotification($firebase_token, SnappyToken $snappy_token)
    {
        $url = env('FIREBASE_NOTIFICATION_URL');

        $product = Product::inRandomOrder()->first();
        $data = [
            "message" => [
                "token" => $snappy_token->token,
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
                $snappy_token->delete();
            }

            return response()->json([
                'success' => false,
                'error' => $errorResponse['error'],
            ], $response->status());
        }
    }
}
