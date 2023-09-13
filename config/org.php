<?php

return [
    'duty' => [
        'driver' => 'session',
        'prefix' => 'admin',
    ],

    'database' => [
        // Database connection for following tables.
        'connection' => '',

        // User tables and model.
        'users_table' => 'org_users',
        'users_model' => \Encore\OrgRbac\Models\User::class,
        'users_tree' => \Encore\OrgRbac\Models\Tree\User::class,
        'user_infos_table' => 'org_user_infos',
        'user_infos_model' => \Encore\OrgRbac\Models\UserInfo::class,
        'users_primary_key_generate_driver' => \Encore\OrgRbac\Services\SnowFlakeService::class,

        // Menu table and model.
        'menu_table' => 'admin_menu',
        'menu_model' => \Encore\OrgRbac\Models\Menu::class,
        'platform_menu_table' => 'platform_menu',
        'platform_menu_model'    => \Encore\OrgRbac\Models\PlatformMenu::class,
        'platform_menu_tree' => \Encore\OrgRbac\Models\Tree\PlatformMenu::class,

        //Platform tables and model
        'platforms_table' => 'admin_platforms',
        'platforms_model' => \Encore\OrgRbac\Models\Platform::class,
        'platforms_tree' => \Encore\OrgRbac\Models\Tree\Platform::class,
        'platforms_primary_key_generate_driver' => \Encore\OrgRbac\Services\SnowFlakeService::class,

        //ORG table and model
        'companies_table' => 'org_companies',
        'companies_model' => \Encore\OrgRbac\Models\Company::class,
        'companies_tree' => \Encore\OrgRbac\Models\Tree\Company::class,
        'companies_primary_key_generate_driver' => \Encore\OrgRbac\Services\SnowFlakeService::class,
        'departments_table' => 'org_departments',
        'departments_model' => \Encore\OrgRbac\Models\Department::class,
        'departments_tree' => \Encore\OrgRbac\Models\Tree\Department::class,
        'departments_primary_key_generate_driver' => \Encore\OrgRbac\Services\SnowFlakeService::class,

        'duties_table' => 'org_duties',
        'duties_model' => \Encore\OrgRbac\Models\Duty::class,


        //Role tables and model
        'roles_table' => 'platform_roles',
        'roles_model' => \Encore\OrgRbac\Models\Role::class,

        // Pivot table for table above.
        'role_duty_table'       => 'platform_role_duty',
        'role_menu_table'        => 'platform_role_menu',
    ],

    /*
    |--------------------------------------------------------------------------
    | organization auth route settings
    |--------------------------------------------------------------------------
    |
    | The routing configuration of the admin page, including the path prefix,
    | the controller namespace, and the default middleware. If you want to
    | access through the root path, just set the prefix to empty string.
    |
    */
    'route' => [

        'prefix' => env('ORG_AUTH_ROUTE_PREFIX', 'auth'),
    ],

    /*
    |--------------------------------------------------------------------------
    | The global Table action display class.
    |--------------------------------------------------------------------------
    */
    'table_action_class' => \Encore\OrgRbac\TabTable\Displayers\DropdownActions::class,

];
