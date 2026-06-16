<?php
// app/Services/FirebaseService.php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/school.json'));

        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification($token, $title, $body)
    {
        $message = [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];

        try {
            $this->messaging->send($message);
        } catch (\Throwable $e) {
            \Log::error('FCM Error: ' . $e->getMessage());
        }
    }
}
