<?php

namespace Encore\OrgRbac\Duty\Driver;

class SessionDriver implements Driver
{

    public function set($key, $value)
    {
        session()->put($key,$value);
    }

    public function get($key)
    {
        return session()->get($key);
    }
}
