<?php

namespace Encore\OrgRbac\Services;

interface DatabasePrimaryKeyGenerateDriver
{
    public static function load();

    public function generate();
}
