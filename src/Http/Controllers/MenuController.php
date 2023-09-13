<?php


namespace Encore\OrgRbac\Http\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form\Field\Icon;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\OrgRbac\Form;
use Encore\OrgRbac\Form\NestedForm;
use Encore\OrgRbac\Layout\Content;
use Encore\OrgRbac\Models\Enums\MenuType;

class MenuController extends AdminController
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
            ->row(function (Row $row) {
                $row->column(6, $this->treeView()->render());

                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->title(trans('admin.new'));
                    $form->action(admin_url('auth/menu'));

                    $menuModel = config('org.database.menu_model');
                    $roleModel = config('org.database.roles_model');

                    $form->select('parent_id', trans('admin.parent_id'))->options($menuModel::selectOptions());
                    $form->text('title', trans('admin.title'))->rules('required')->prepend(new Icon('icon'));
                    $form->text('uri', trans('admin.uri'));
                    $form->select('type',"类型")->options(MenuType::$text)->default(MenuType::MENU);
                    $form->multipleSelect('roles','角色')->options($roleModel::getPlatformRole());
                    $form->hidden('_saved')->default(1);

                    $column->append($form);
                });
            });
    }
    /**
     * Redirect to edit page.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id,Content $content)
    {
        return redirect()->route(config('admin.route.as') . 'auth_menus.edit', ['menu' => $id]);
    }

    /**
     * @return \Encore\Admin\Tree
     */
    protected function treeView()
    {
        $menuModel = config('admin.database.menu_model');

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
        $platformModel = config('org.database.platforms_model');
        $menuModel = config('org.database.menu_model');
        $roleModel = config('org.database.roles_model');

        $form = new Form(new $menuModel());
        $form->tab('基本信息',function (Form $form) use($menuModel,$roleModel) {
            $form->display('id', 'ID');

            $form->select('parent_id', trans('admin.parent_id'))->options($menuModel::selectOptions(function ($query) {
                $query->where('type',MenuType::MENU);
            }));
            $form->text('title', trans('admin.title'))->rules('required')->prepend(new Icon('icon'));
            $form->text('uri', trans('admin.uri'));
            $form->hidden('type')->value(MenuType::MENU);
            if (Admin::user()->isRootAdministrator()) {
                $form->multipleSelect('roles','角色')->options($roleModel::getAdministratorRole());
            } else {
                $form->multipleSelect('roles','角色')->options($roleModel::getPlatformRole());
            }


            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));

        })->tab('功能按钮',function (Form $form) use($roleModel) {
            $form->hasMany('child','按钮',function (NestedForm $form) use($roleModel) {
                $form->display('id', 'ID');
                $form->text('title', trans('admin.title'))->rules('required');
                $form->text('uri', trans('admin.uri'));
                if (Admin::user()->isRootAdministrator()) {
                    $form->multipleSelect('roles','角色')->options($roleModel::getAdministratorRole());
                } else {
                    $form->multipleSelect('roles','角色')->options($roleModel::getPlatformRole());
                }
                $form->hidden('type')->value(MenuType::BTN);
                $form->hidden('icon')->value("fas fa-align-justify");
            });
        });
        if (Admin::user()->isRootAdministrator()) {
            $form->tab('平台分配',function (Form $form) use ($platformModel) {
                $form->multipleSelect('platforms','平台')->options($platformModel::all()->pluck('name','id'));
            });
        }

        return $form;
    }
}
