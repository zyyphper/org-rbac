<?php

namespace Encore\OrgRbac\Duty\Driver;

interface Driver
{
    public function set($key,$value);

    public function get($key);
}
