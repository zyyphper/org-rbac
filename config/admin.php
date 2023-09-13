<?php

return [

    'database' => [
        // Database connection for following tables.
        'connection' => '',

        // User tables and model.
        'users_table' => 'admin_users',
        'users_model' => \Encore\OrgRbac\Models\User::class,

        // Menu table and model.
        'menu_table' => 'admin_menu',
        'menu_model' => \Encore\OrgRbac\Models\Menu::class,

        //ORG table and model
        'companies_table' => 'org_companies',
        'companies_model' => \Encore\OrgRbac\Models\Company::class,
        'departments_table' => 'org_departments',
        'departments_model' => \Encore\OrgRbac\Models\Department::class,
        'duties_table' => 'org_duties',
        'duties_model' => \Encore\OrgRbac\Models\Duty::class,

        //Platform tables and model
        'platforms_table' => 'admin_platforms',
        'platforms_model' => \Encore\OrgRbac\Models\Platform::class,
        //Role tables and model
        'roles_table' => 'admin_roles',
        'roles_model' => \Encore\OrgRbac\Models\Role::class,

        // Pivot table for table above.
        'role_duty_table'       => 'admin_role_duty',
        'role_menu_table'        => 'admin_role_menu',
        'platform_menu_table'    => 'admin_platform_menu',
    ],
];
