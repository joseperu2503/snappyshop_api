<?php

namespace App\Http\Controllers;

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
            $token = $this->getFirebaseToken();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while getting the token.',
            ], 500);
        }

        $url = env('FIREBASE_NOTIFICATION_URL');

        $data = [
            "message" => [
                "token" => "eeC8PPAMTiK9LiEf4GAF1L:APA91bHE8BIRpE4W9G51wp5dzAhfdPsB1tdZcji6XcFny8USntaeQg4lC9lJEdN5OU4CoMUuFdOKsdc4TyPx8iDDVa8L1OLPb9t6ViNHYLF9sJlJufJm7j7WsYq86wmr607kxQk-NDWk",
                "data" => [
                    "type" => "product",
                    "productId" => "3"
                ],
                "notification" => [
                    "title" => "FCM Message",
                    "body" => "This is an FCM notification message!"
                ],
                "android" => [
                    "notification" => [
                        "image" => "https://oechsle.vteximg.com.br/arquivos/ids/6407352-1500-1500/image-7e911399945845ea8d0d2af8a3420e28.jpg"
                    ]
                ]
            ]
        ];

        $response = Http::withToken($token)->post($url, $data);

        if ($response->successful()) {
            $responseData = $response->json();
            return response()->json($responseData);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while connecting to send the notification to Firebase.',
            ], $response->status());
        }
    }
}
