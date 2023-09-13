<?php


namespace Encore\OrgRbac\Http\Controllers;


use Encore\Admin\Table;
use Encore\OrgRbac\Form;
use Encore\OrgRbac\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '平台';

    /**
     * @var Platform
     */
    protected $model;

    protected $parentId;


    public function __construct(Request $request)
    {
        $platformModel = config('org.database.platforms_model');
        $this->model = new $platformModel();
        $this->parentId = $request->input('parent_id');
    }
    /**
     * 表格
     * @return Table
     * @throws \Exception
     */
    protected function table()
    {
        $table = new Table($this->model);
        $table->model()->latest();


        $table->column('id', 'ID');
        $table->column('name', '平台名称');
        $status = [
            'on' => ['value'=>1,'text'=>'启用','color'=>'primary'],
            'off' => ['value'=>0,'text'=>'禁用','color'=>'default']
        ];
        $table->column('status', '状态')->switch($status);
        $table->column('created_at', '创建时间');


        $table->actions(function ($actions) {
        });

        return $table;
    }

    /**
     *
     */
    protected function form()
    {
        $form = new Form($this->model);
        $form->text('name', '平台名称');
        $form->saving(function (Form $form) {
            if ($form->isCreating()) $form->model()->id = $form->model()->id = app('primaryKeyGenerate')->load(config('org.database.platforms_primary_key_generate_driver'))->generate();
        });
        return $form;
    }

}
