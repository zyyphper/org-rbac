<?php


namespace Encore\OrgRbac\Models\Enums;


class IsAdmin
{
    const YES = 1;
    const NO = 0;

    public static $index = [
        self::YES => '管理员',
        self::NO => '普通角色',
    ];

}
