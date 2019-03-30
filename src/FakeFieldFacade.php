<?php

namespace Codemen\FakeField;

use Illuminate\Support\Facades\Facade;

class FakeFieldFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'FakeField';
    }
}
