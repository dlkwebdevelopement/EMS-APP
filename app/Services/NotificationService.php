<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Employee;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a notification to an employee (or Admin if employeeId is null)
     * and dispatch an Expo Push Notification if a push_token is registered.
     */
    public static function send($employeeId, string $title, string $message, string $type = 'system', array $data = [])
    {
        try {
            // 1. Create DB Notification
            $notification = Notification::create([
                'employee_id' => $employeeId,
                'title'       => $title,
                'message'     => $message,
                'type'        => $type,
                'is_read'     => false,
                'data'        => $data,
            ]);

            // 2. Dispatch Expo Push Notification if push_token exists
            if ($employeeId) {
                $employee = Employee::find($employeeId);
                if ($employee && !empty($employee->push_token)) {
                    self::sendExpoPushNotification($employee->push_token, $title, $message, $data);
                }
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error("NotificationService Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send Push Notification via Expo HTTP API v2
     */
    private static function sendExpoPushNotification(string $expoPushToken, string $title, string $body, array $data = [])
    {
        try {
            Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])->post('https://exp.host/--/api/v2/push/send', [
                'to'    => $expoPushToken,
                'sound' => 'default',
                'title' => $title,
                'body'  => $body,
                'data'  => $data,
            ]);
        } catch (\Exception $e) {
            Log::error("Expo Push Notification Error: " . $e->getMessage());
        }
    }
}
