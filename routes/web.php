<?php


Route::group([
    'prefix' => config('org.route.prefix'),
], function (Illuminate\Routing\Router $router) {
    $router->resource('companies', \Encore\OrgRbac\Http\Controllers\CompanyController::class);
    $router->resource('departments', \Encore\OrgRbac\Http\Controllers\DepartmentController::class);
    $router->resource('users', \Encore\OrgRbac\Http\Controllers\UserController::class);
    $router->resource('organizations', \Encore\OrgRbac\Http\Controllers\OrgController::class);
    $router->resource('duties', \Encore\OrgRbac\Http\Controllers\DutyController::class);
    //平台
    $router->resource('platforms', \Encore\OrgRbac\Http\Controllers\PlatformController::class);
    //角色
    $router->resource('roles', \Encore\OrgRbac\Http\Controllers\RoleController::class);
    //菜单
    $router->resource('menu', \Encore\OrgRbac\Http\Controllers\MenuController::class);
    $router->post('users/dutySelect',\Encore\OrgRbac\Http\Controllers\AuthController::class."@dutySelect");
    //平台菜单
    $router->resource('platformMenu', \Encore\OrgRbac\Http\Controllers\PlatformMenuController::class);

});


Route::group([
    'prefix' => 'api',
], function (Illuminate\Routing\Router $router) {
    $router->get('organizations/getCompanyList', \Encore\OrgRbac\Http\Controllers\OrgController::class."@getCompanyList");
    $router->get('organizations/getDepartmentList', \Encore\OrgRbac\Http\Controllers\OrgController::class."@getDepartmentList");
});
