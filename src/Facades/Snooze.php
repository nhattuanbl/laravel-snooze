<?php

namespace Nhattuanbl\Snooze\Facades;

use Illuminate\Support\Facades\Facade;

class Snooze extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'NotifySnooze';
    }
}
