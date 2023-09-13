<?php


namespace Encore\OrgRbac\Http\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Form\Field\Icon;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\OrgRbac\Form;
use Encore\OrgRbac\Form\NestedForm;
use Encore\OrgRbac\Layout\Content;
use Encore\OrgRbac\Models\Enums\MenuType;

class PlatformMenuController extends Controller
{
    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->title(trans('admin.menu'))
            ->description(trans('admin.list'))
            ->row($this->treeView()->render());
    }
    /**
     * Redirect to edit page.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        return redirect()->route(config('admin.route.as') . 'auth_menus.edit', ['menu' => $id]);
    }

    /**
     * @return \Encore\Admin\Tree
     */
    protected function treeView()
    {
        $menuModel = config('admin.database.platform_menu_tree');

        $tree = new Tree(new $menuModel());

        $tree->disableCreate();

        $tree->branch(function ($branch) {
            $payload = "<i class='{$branch['icon']}'></i>&nbsp;<strong>{$branch['title']}</strong>";

            if (!isset($branch['children'])) {
                if (url()->isValidUrl($branch['uri'])) {
                    $uri = $branch['uri'];
                } else {
                    $uri = admin_url($branch['uri']);
                }

                $payload .= "&nbsp;&nbsp;&nbsp;<a href=\"$uri\" class=\"dd-nodrag\">$uri</a>";
            }

            return $payload;
        });

        return $tree;
    }

    /**
     * Edit interface.
     *
     * @param string  $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->title(trans('admin.menu'))
            ->description(trans('admin.edit'))
            ->row($this->form()->edit($id));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $menuModel = config('org.database.menu_model');
        $roleModel = config('org.database.roles_model');

        $form = new Form(new $menuModel());
        $form->tab('基本信息',function (Form $form) use($menuModel,$roleModel) {
            $form->text('title', trans('admin.title'))->disable();
            $form->multipleSelect('roles','角色')->options($roleModel::getPlatformRole());
        })->tab('功能按钮',function (Form $form) use($roleModel) {
            $form->hasMany('child','按钮',function (NestedForm $form) use($roleModel) {
                $form->text('title', trans('admin.title'))->disable();
                $form->multipleSelect('roles','角色')->options($roleModel::getPlatformRole());
            });
        });


        return $form;
    }
}
