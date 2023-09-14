<?php


namespace Encore\OrgRbac\Models\Enums;


class BootStatus
{
    const ENABLE = 1;
    const DISABLE = 2;

    public static $index = [
        self::ENABLE => '启用',
        self::DISABLE => '禁用',
    ];

}
