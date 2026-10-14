<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class PushService
{
    /**
     * Send a push notification via Firebase Cloud Messaging.
     *
     * @param string|array $to The FCM Push Token(s)
     * @param string $title The title of the notification
     * @param string $body The main text body
     * @param array $data Optional JSON data payload
     * @return bool Returns true on successful request, false on error
     */
    public static function send($to, string $title, string $body, array $data = [])
    {
        if (empty($to)) {
            return false;
        }

        if (!is_array($to)) {
            $to = [$to];
        }

        // Filter valid tokens (must not be empty)
        $validTokens = array_filter($to, function($token) {
            return !empty(trim($token));
        });

        if (empty($validTokens)) {
            return false;
        }

        try {
            $messaging = app('firebase.messaging');

            $notification = Notification::create($title, $body);

            // Convert data array to string values (Firebase requirement)
            $stringData = [];
            foreach ($data as $key => $value) {
                $stringData[(string) $key] = (string) $value;
            }

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($stringData);

            $messaging->sendMulticast($message, $validTokens);

            Log::info("FCM Notification sent to " . count($validTokens) . " devices.");
            return true;
        } catch (\Exception $e) {
            Log::error('FCM Push Notification Error: ' . $e->getMessage());
            return false;
        }
    }
}
