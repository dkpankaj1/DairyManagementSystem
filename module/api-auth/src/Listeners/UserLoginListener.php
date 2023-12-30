<?php

namespace Cortexitsolution\ApiAuth\Listeners;

use Cortexitsolution\ApiAuth\Events\UserLoginEvent;
use Illuminate\Support\Facades\DB;

class UserLoginListener
{
    public function __construct()
    {
        //
    }

   
    public function handle(UserLoginEvent $event)
    {
        DB::table('login_histories')->insert([
            'email' => $event->user->email,
            'login_time' => now(),
            'ip' => $event->ip
        ]);
    }
}