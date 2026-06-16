<?php

namespace App\Listeners;

use App\Services\FirebaseService;
use Illuminate\Notifications\Events\NotificationSent;

class SendFcmNotification
{
    /**
     * Handle the event.
     */
    public function handle(NotificationSent $event)
    {
        $token = $event->notifiable->fcm_token;

        if (!$token) {
            return;
        }

        $message = $event->notification->toArray($event->notifiable)['message'] ?? 'You have a new notification';

        // Send via FirebaseService
        (new FirebaseService())->sendNotification(
            $token,
            'Update',
            $message
        );
    }
}
