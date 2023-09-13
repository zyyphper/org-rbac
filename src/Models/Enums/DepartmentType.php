<?php


namespace Encore\OrgRbac\Models\Enums;


class DepartmentType
{
    const MAIN = 1;
    const SUBSIDIARY = 2;

    public static $text = [
        self::MAIN => '主部门',
        self::SUBSIDIARY => '附属部门',
    ];

}
