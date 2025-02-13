<?php

namespace App\Traits;

use App\Models\Notification;
use Carbon\Exceptions\Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Google\Client as GoogleClient;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

trait PushNotification
{

    private function sendNewNotification(Notification $notification)
    {
        $deviceToken = $notification->user->device_token;
        $title = $notification->title;
        $body = $notification->body;
        $data = ['id' => $notification->id];
        $firebase = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'));

        $messaging = $firebase->createMessaging();

        if (empty($deviceToken)) {
            return response()->json(['error' => 'Device token is missing'], 400);
        }

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification([
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ]);

        try {
            $messaging->send($message);
            return response()->json(['message' => 'Notification sent successfully']);
        } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
            return response()->json(['error' => 'Invalid message: ' . $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error sending notification: ' . $e->getMessage()], 500);
        }

    }

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

    public function pushNotification(Notification $notification)
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

        // $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        // $credentials = ApplicationDefaultCredentials::getCredentials($scopes);
        // dd($credentials);
        // $token = $credentials->fetchAuthToken();
        $client = new GoogleClient();
        $client->setAuthConfig($keyPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        return $access_token;
    }
}
