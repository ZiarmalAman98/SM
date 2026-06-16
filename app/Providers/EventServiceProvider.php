<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Notifications\Events\NotificationSent;
use App\Listeners\SendFcmNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NotificationSent::class => [
            SendFcmNotification::class,
        ],
        // Other events and listeners here...
    ];

    public function boot()
    {
        parent::boot();
    }
}
