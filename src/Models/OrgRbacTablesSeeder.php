<?php

namespace Encore\Admin\Models;

use Encore\OrgRbac\Models\Company;
use Encore\OrgRbac\Models\Department;
use Encore\OrgRbac\Models\Duty;
use Encore\OrgRbac\Models\Enums\BootStatus;
use Encore\OrgRbac\Models\Enums\DepartmentType;
use Encore\OrgRbac\Models\Enums\IsAdmin;
use Encore\OrgRbac\Models\Enums\MenuType;
use Encore\OrgRbac\Models\Menu;
use Encore\OrgRbac\Models\Platform;
use Encore\OrgRbac\Models\Role;
use Encore\OrgRbac\Models\User;
use Encore\OrgRbac\Models\UserInfo;
use Illuminate\Database\Seeder;

class OrgRbacTablesSeeder extends Seeder
{
    protected $platform;
    protected $company;
    protected $department;
    protected $duty;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //org create
        $this->createPlatform();
        $this->createCompany();
        $this->createDepartment();
        $this->createUser();
        //rbac create
        $this->createMenu();
        $this->createRole();
    }

    protected function createPlatform()
    {
        Platform::truncate();
        $this->platform = Platform::create([
            'id'       => app('primaryKeyGenerate')->load(config('org.database.platforms_primary_key_generate_driver'))->generate(),
            'name'     => '管理平台',
            'status'   => BootStatus::ENABLE,
            'is_admin' => IsAdmin::YES
        ]);
    }

    protected function createCompany()
    {
        Company::truncate();
        $this->company = Company::create([
            'id'          => app('primaryKeyGenerate')->load(config('org.database.companies_primary_key_generate_driver'))->generate(),
            'parent_id'   => 0,
            'platform_id' => $this->platform->id,
            'name'        => '管理中心',
        ]);
    }

    protected function createDepartment()
    {
        Department::truncate();
        $this->department = Department::create([
            'id'         => app('primaryKeyGenerate')->load(config('org.database.departments_primary_key_generate_driver'))->generate(),
            'parent_id'  => 0,
            'company_id' => $this->company->id,
            'name'       => '管理部门',
        ]);
    }

    protected function createUser()
    {
        //create user and info
        User::truncate();
        $user = User::create([
            'id' => app('primaryKeyGenerate')->load(config('org.database.users_primary_key_generate_driver'))->generate(),
            'platform_id' => $this->platform->id,
            'company_id'  => $this->company->id,
            'username'    => 'admin',
            'password'    => bcrypt('admin'),
            'name'        => 'Administrator',
            'is_admin'    => IsAdmin::YES,
            'status'      => BootStatus::ENABLE
        ]);
        UserInfo::create([
           'user_id' => $user->id
        ]);
        //create connection between user and department
        $this->duty = Duty::create([
            'user_id' => $user->id,
            'department_id' => $this->department->id,
            'department_type' => DepartmentType::MAIN
        ]);
    }

    protected function createMenu()
    {
        Menu::truncate();
        Menu::insert([
            [
                'id'        => 1,
                'parent_id' => 0,
                'order'     => 1,
                'title'     => 'Home',
                'icon'      => 'fas fa-tachometer-alt',
                'uri'       => '/',
                'is_admin'  => IsAdmin::NO,
                'type'      => MenuType::MENU
            ],
            [
                'id'        => 2,
                'parent_id' => 0,
                'order'     => 2,
                'title'     => 'Admin',
                'icon'      => 'empty',
                'uri'       => null,
                'is_admin'  => IsAdmin::YES,
                'type'      => MenuType::MENU
            ],
            [
                'id'        => 3,
                'parent_id' => 2,
                'order'     => 1,
                'title'     => 'Organization',
                'icon'      => 'fas fa-bars',
                'uri'       => 'auth/organizations',
                'is_admin'  => IsAdmin::YES,
                'type'      => MenuType::MENU
            ],
            [
                'parent_id' => 2,
                'order'     => 2,
                'title'     => 'Menu',
                'icon'      => 'fas fa-bars',
                'uri'       => 'auth/menu',
                'is_admin'  => IsAdmin::YES,
                'type'      => MenuType::MENU
            ],
            [
                'parent_id' => 2,
                'order'     => 3,
                'title'     => 'Role',
                'icon'      => 'fas fa-bars',
                'uri'       => 'auth/roles',
                'is_admin'  => IsAdmin::YES,
                'type'      => MenuType::MENU
            ],
            [
                'parent_id' => 3,
                'order'     => 1,
                'title'     => 'orgTreeDetail',
                'icon'      => 'fas fa-align-justify',
                'uri'       => 'org_tree_detail',
                'is_admin'  => IsAdmin::YES,
                'type'      => MenuType::BTN
            ],
            [
                'parent_id' => 3,
                'order'     => 2,
                'title'     => 'platformMenu',
                'icon'      => 'fas fa-align-justify',
                'uri'       => 'platform_menu',
                'is_admin'  => IsAdmin::YES,
                'type'      => MenuType::BTN
            ],
        ]);
    }

    protected function createRole()
    {
        Role::truncate();
        $administrator = Role::create([
            'platform_id' => $this->platform->id,
            'name'        => 'Administrator',
            'slug'        => 'administrator',
            'is_admin'    => IsAdmin::YES
        ]);
        $administrator->duties()->save($this->duty);
    }
}
