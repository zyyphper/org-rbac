<?php

namespace Encore\OrgRbac\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class OrgRbac.
 * @method static duty()
 *
 * @see \Encore\OrgRbac\OrgRbac
 */
class OrgRbac extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Encore\OrgRbac\OrgRbac::class;
    }
}
