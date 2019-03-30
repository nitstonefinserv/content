<?php
namespace Reflexions\Content;

use Illuminate\Support\Facades\Facade;

class ContentFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return ContentServiceProvider::NAME; }
}