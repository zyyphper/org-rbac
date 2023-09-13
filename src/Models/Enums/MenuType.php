<?php


namespace Encore\OrgRbac\Models\Enums;


class MenuType
{
    const MENU = 1;
    const BTN = 2;

    public static $text = [
        self::MENU => '菜单',
        self::BTN => '功能按钮',
    ];

}
