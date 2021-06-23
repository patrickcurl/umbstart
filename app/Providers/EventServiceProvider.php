<?php

declare(strict_types=1);
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \Illuminate\Auth\Events\Registered::class => [
            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
        ],
        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\Auth\LoginSuccess::class,
        ],
        \Illuminate\Auth\Events\Failed::class => [
            \App\Listeners\Auth\LoginFailed::class,
        ],
        \Illuminate\Auth\Events\Lockout::class => [
            \App\Listeners\Auth\LoginLockout::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
