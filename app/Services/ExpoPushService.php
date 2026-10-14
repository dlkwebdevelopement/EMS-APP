<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushService
{
    /**
     * Send a push notification to an Expo Push Token.
     *
     * @param string|array $to The Expo Push Token(s) (e.g., ExponentPushToken[xxxxxxx])
     * @param string $title The title of the notification
     * @param string $body The main text body
     * @param array $data Optional JSON data payload
     * @return bool Returns true on successful request, false on error
     */
    public static function send($to, string $title, string $body, array $data = [])
    {
        // Ignore empty tokens or invalid tokens
        if (empty($to)) {
            return false;
        }

        // Convert single token to array for batch processing
        if (!is_array($to)) {
            $to = [$to];
        }

        // Filter valid expo tokens
        $validTokens = array_filter($to, function($token) {
            return str_starts_with($token, 'ExponentPushToken[') || str_starts_with($token, 'ExpoPushToken[');
        });

        if (empty($validTokens)) {
            return false;
        }

        $messages = [];
        foreach ($validTokens as $token) {
            $messages[] = [
                'to' => $token,
                'sound' => 'default',
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate',
                'Content-Type' => 'application/json',
            ])->post('https://exp.host/--/api/v2/push/send', $messages);

            if ($response->successful()) {
                return true;
            }

            Log::error('Expo Push Notification Failed', [
                'response' => $response->body()
            ]);
            
            return false;
        } catch (\Exception $e) {
            Log::error('Expo Push Exception: ' . $e->getMessage());
            return false;
        }
    }
}
