<?php

namespace App\Listeners\Auth;

use App\Models\Audit;
use Carbon\Carbon;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LoginFailed
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        if ($event->user) {
            Audit::create([
                'auditable_id'     => $event->user->id,
                'auditable_type'   => 'Failed Login',
                'event'            => 'Failed Login',
                'url'              => request()->fullUrl(),
                'ip_address'       => request()->getClientIp(),
                'user_agent'       => request()->userAgent(),
                'user_type'        => 'App\Model\User',
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now(),
                'user_id'          => $event->user->id,
            ]);
        }
    }
}
