<?php


namespace Encore\OrgRbac\Http\Controllers;


use Encore\Admin\Facades\Admin;
use Encore\Admin\Table;
use Encore\OrgRbac\Form;
use Encore\OrgRbac\Models\Enums\IsAdmin;
use Encore\OrgRbac\Models\Platform;
use Encore\OrgRbac\Traits\PlatformPermission;
use Illuminate\Http\Request;

class RoleController extends AdminController
{
    use PlatformPermission;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '角色';

    /**
     * @var Platform
     */
    protected $model;

    /**
     * @var
     */
    protected $service;

    public function __construct(Request $request)
    {
        $model = config('org.database.roles_model');
        $this->model = new $model();
        parent::__construct($request);
    }

    /**
     * Make a grid builder.
     *
     * @return Table
     */
    protected function table()
    {
        $table = new Table($this->model);
        $this->platformAuth($table);

        $table->column('id', 'ID')->sortable();
        $table->column('slug', trans('admin.slug'));
        $table->column('name', trans('admin.name'));


        $table->column('created_at', trans('admin.created_at'));
        $table->column('updated_at', trans('admin.updated_at'));
        $table->disableCreateButton(Admin::user()->cannot('role_create'));

        //超级管理员角色不可修改 超级管理员可删除平台管理员
        $table->actions(function (Table\Displayers\Actions $actions) {
            if ($actions->row->slug == 'administrator') {
                $actions->disableEdit();
                $actions->disableView();
                $actions->disableDelete();
            } elseif ($actions->row->is_admin == IsAdmin::YES) {
                if (Admin::user()->isRootAdministrator()) {
                } elseif (Admin::user()->isAdministrator()) {
                    $actions->disableDelete();
                } else {
                    $actions->disableEdit();
                    $actions->disableView();
                    $actions->disableDelete();
                }
            }
        });

        $table->tools(function (Table\Tools $tools) {
            $tools->batch(function (Table\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $table;
    }

    /**
     * Make a form builder.
     *
     * @return \Encore\Admin\Form
     */
    public function form()
    {
        $form = new Form($this->model);

        $form->display('id', 'ID');
        $this->platformAuth($form);
        $form->text('slug', trans('admin.slug'))->rules('required');
        $form->text('name', trans('admin.name'))->rules('required');

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            $form->model()->platform_id = $this->getPlatformId();
        });

        return $form;
    }

}
