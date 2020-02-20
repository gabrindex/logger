<?php

namespace gabrindex\logger\Facades;

use Illuminate\Support\Facades\Facade;

class logger extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'logger';
    }
}
