<?php

namespace Cortexitsolution\ApiAuth\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoginEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $ip;
  
    public function __construct($user,$ip)
    {
        $this->user = $user;
        $this->ip = $ip;
    }

}