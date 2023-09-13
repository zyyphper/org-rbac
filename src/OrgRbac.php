<?php

namespace Encore\OrgRbac;

use Encore\Admin\Extension;
use Encore\Admin\Facades\Admin;
use Encore\OrgRbac\Duty\Duty;
use Encore\OrgRbac\Models\Tree\PlatformMenu;
use Illuminate\Support\Traits\Macroable;

class OrgRbac extends Extension
{
    use Macroable;
    public $name = 'org_rbac';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    public $menu = [];

    public function duty()
    {
        $dutyId = Duty::load()->getId();
        $dutyModel = config('org.database.duties_model');
        $data = $dutyModel::where('user_id',Admin::user()->id)->with(['department'=>function($query) {
            $query->select('id','name');
        }])->get()->toArray();
        $result = [];
        foreach ($data as $value) {
            if ($dutyId == $value['id']) {
                array_unshift($result,[
                    'id' => $value['id'],
                    'name' => $value['department']['name'],
                    'selected' => true
                ]);
                continue;
            }
            array_push($result,[
                'id' => $value['id'],
                'name' => $value['department']['name'],
                'selected' => false
            ]);
        }
        return $result;
    }

    /**
     * Left sider-bar menu.
     *
     * @return array
     */
    public function menu()
    {
        if (!empty($this->menu)) {
            return $this->menu;
        }

        $menuClass = config('org.database.platform_menu_tree');

        /** @var PlatformMenu $menuClass */
        $menuTree = new $menuClass();

        return $this->menu = $menuTree->toTree();
    }
}
