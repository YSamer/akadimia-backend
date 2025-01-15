<?php

namespace App\Traits;

use Carbon\Exceptions\Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

trait PushNotification
{
    public function sendNotification($deviceToken, $title, $body, $data)
    {
        $url = 'https://fcm.googleapis.com/v1/projects/akadimia-app/messages:send';

        $notification = [
            "message" => [
                "token" => $deviceToken,
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                ],
                "data" => $data,
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ])->post($url, $notification);

            return $response->json();
        } catch (Exception $e) {
            // Handle error...
            Log::error('Error sending notification');
            return false;
        }
    }

    public function pushNotification($notification)
    {
        $deviceToken = $notification->user->device_token;
        $title = $notification->title;
        $body = $notification->body;
        $data = ['id' => $notification->id];

        $url = 'https://fcm.googleapis.com/v1/projects/akadimia-app/messages:send';

        $notification = [
            "message" => [
                "token" => $deviceToken,
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                ],
                "data" => $data,
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ])->post($url, $notification);

            return $response->json();
        } catch (Exception $e) {
            // Handle error...
            Log::error('Error sending notification');
            return false;
        }
    }

    public function getAccessToken()
    {
        $keyPath = config('services.firebase.key_path');
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $keyPath);

        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        $credentials = ApplicationDefaultCredentials::getCredentials($scopes);
        $token = $credentials->fetchAuthToken();
        return $token['access_token'] ?? null;
    }
}
