<?php

namespace Trochilidae;

use Trochilidae\Events\HttpEvent;

class Server
{
    private $handler;

    private $listeners = [
        'Request'
    ];

    public function start(): void
    {
        $this->handler = new \Swoole\Http\Server('127.0.0.1', '8906');

        $httpEvent = new HttpEvent();
        foreach ($this->listeners as $event) {
            $this->handler->on($event, [$httpEvent, strtolower($event)]);
        }

        $this->handler->start();
    }
}