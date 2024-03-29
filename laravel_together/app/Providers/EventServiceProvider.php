<?php

namespace App\Providers;

use App\Events\AlarmEvent;
use App\Events\MessageSent;
use App\Events\SpecificDateReached;
use App\Listeners\AlarmEventListener;
use App\Listeners\MessageSentListener;
use App\Listeners\ProcessSpecificDateReached;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MessageSent::class => [
            MessageSentListener::class,
        ],
        AlarmEvent::class => [
            AlarmEventListener::class,
        ],
        SpecificDateReached::class => [
            ProcessSpecificDateReached::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
