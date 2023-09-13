<?php

namespace Encore\OrgRbac\Models\Tree;


use Encore\Admin\Facades\Admin;
use Encore\OrgRbac\Models\PlatformMenu AS BaseModel;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Traits\ModelTree;
use Encore\OrgRbac\Models\Enums\MenuType;
use Illuminate\Support\Facades\DB;

class PlatformMenu extends BaseModel
{
    use DefaultDatetimeFormat,
        ModelTree {
        ModelTree::boot as treeBoot;
    }

    /**
     * 返回有权限的菜单 超级管理员直接返回全部 平台管理员返回平台配置下的所有菜单 再获取用户拥有的所有角色的菜单权限
     * @return array
     */
    public function allNodes(): array
    {
        $menuModel = config('org.database.menu_model');
        $connection = config('org.database.connection') ?: config('database.default');
        $orderColumn = DB::connection($connection)->getQueryGrammar()->wrap($this->getOrderColumn());

        $byOrder = 'ROOT ASC,'.$orderColumn;

        $query = $menuModel::where('type',MenuType::MENU);

        if (!Admin::user()->isRootAdministrator()) {
            $query = $menuModel::whereHas('platforms',function ($query) {
                $query->where('platform_id',Admin::user()->platform_id);
            });
        }
        if (!Admin::user()->isAdministrator()) {
            $query->whereHas('roles',function ($roleQuery) {
                $roleQuery->whereHas('duties',function ($query) {
                    $query->where('id',\Encore\OrgRbac\Duty\Duty::load()->getId());
                });
            });
        }
        return $query->selectRaw('*, '.$orderColumn.' ROOT')
            ->orderByRaw($byOrder)
            ->get()->toArray();
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        static::treeBoot();
    }
}
