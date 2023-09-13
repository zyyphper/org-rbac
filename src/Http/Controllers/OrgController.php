<?php

namespace Encore\OrgRbac\Http\Controllers;

use Encore\Admin\Layout\Column;
use Encore\OrgRbac\Actions\RelationTree\DetailAction;
use Encore\OrgRbac\Actions\Tree\EditAction;
use Encore\OrgRbac\Actions\RelationTree\PlatformMenuAction;
use Encore\OrgRbac\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\OrgRbac\Models\Enums\DepartmentType;
use Encore\OrgRbac\Show;
use Encore\OrgRbac\Traits\PlatformPermission;
use Encore\OrgRbac\Tree;
use Encore\OrgRbac\Widgets\Tab;
use Encore\OrgRbac\Models\Enums\OrgType;
use Encore\OrgRbac\RelationTree;
use Encore\OrgRbac\TabTable\TabTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrgController extends AdminController
{
    use PlatformPermission;
    protected $action;
    protected $type;
    protected $mainId;
    protected $tab;
    protected $platformModel;
    protected $companyModel;
    protected $departmentModel;
    protected $userModel;
    protected $dutyModel;
    protected $roleModel;


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->action = $this->request->input("action");
        $this->type =  $this->request->input("type");
        $this->mainId =  $this->request->input("main_id");
        $this->tab = $this->request->input("tab",0);
        $platformModel = config('org.database.platforms_tree');
        $this->platformModel = new $platformModel();
        $companyModel = config('org.database.companies_tree');
        $this->companyModel = new $companyModel();
        $departmentModel = config('org.database.departments_tree');
        $this->departmentModel = new $departmentModel();
        $userModel = config('org.database.users_model');
        $this->userModel = new $userModel();
        $dutyModel = config('org.database.duties_model');
        $this->dutyModel = new $dutyModel();
        $roleModel = config('org.database.roles_model');
        $this->roleModel = new $roleModel();
    }

    public function index(Content $content)
    {
        $content->row(function(Row $row){
            //组织树
            $row->column(4,$this->treeView()->render());
            if (is_null($this->type) || is_null($this->mainId)) {
                $row->column(8,function (Column $column) {
                });
            } else {
                switch ($this->action) {
                    case "detail":
                        //根据选择的是 平台 公司 部门  动态显示
                        $method = OrgType::$index[$this->type];
                        $row->column(8,function (Column $column) use($method) {
                            $content = $this->$method();
                            $column->row($content);
                        });
                        break;
                    case "platformMenu":
                        $row->column(8,function (Column $column) {
                            $column->row($this->platformMenuTreeView()->render());
                        });
                        break;
                }

           }
        });
        return $content;
    }

    /**
     * @return \Encore\Admin\Tree
     */
    protected function treeView()
    {
        $platformTreeModel = config('org.database.platforms_tree');

        $tree = new RelationTree(new $platformTreeModel());

        $tree->disableCreate();
        $tree->disableSave();
        $tree->nestable([
            'maxDepth' => 0, // 设置可拖动层级为 2 层，设置其他参数可参考 jquery.nestable 文档
        ]);
        $tree->actions(OrgType::$detailShow,DetailAction::class);
        $tree->action(OrgType::PLATFORM,PlatformMenuAction::class);


        $tree->branch(function ($branch) {
            //获取当前平台下的公司
            switch ($branch['type']) {
                case OrgType::PLATFORM:$class = "fas fa-home";break;
                case OrgType::COMPANY:$class = "far fa-building";break;
                case OrgType::DEPARTMENT:$class = "far fa-folder-open";break;
                case OrgType::USER:$class = "far fa-address-card";break;
            }
            return "<i class='{$class}'></i>&nbsp;<strong>{$branch['name']}</strong>";
        });

        return $tree;
    }

    public function platform()
    {
        $tab = new Tab();
        $platformTable = new TabTable($this->platformModel);
        $platformTable->model()->where($this->platformModel->getKeyName(),$this->mainId);
        $platformTable->setTitle('平台');
        $platformTable->setResourceUrl(url("admin/auth/platforms"));
        $platformTable->setCreateHandleParams([
            'parent_id' => $this->mainId
        ]);
        $platformTable->setCreateButtonUri('create_platform');
        $platformTable->column('id','ID');
        $platformTable->column('name',trans('admin.name'));
        $tab->add('平台 ',$platformTable,$this->tab == 0);
        $companyTable = new TabTable($this->companyModel);
        $companyTable->model()->whereHas('platform',function ($query) {
            $query->where($this->platformModel->getKeyName(),$this->mainId);
        });
        $companyTable->setTitle('公司');
        $companyTable->setResourceUrl(url("admin/auth/companies"));
        $companyTable->setCreateHandleParams([
            'platform_id' => $this->mainId
        ]);
        $companyTable->setCreateButtonUri('create_company');
        $companyTable->column('id','ID');
        $companyTable->column('name',trans('admin.name'));
        $tab->add('子公司 ',$companyTable,$this->tab == 1);
        return $tab;
    }

    /**
     * @return \Encore\Admin\Tree
     */
    protected function platformMenuTreeView()
    {
        $menuModel = config('org.database.platform_menu_tree');

        $tree = new Tree(new $menuModel());

        $tree->disableCreate();
        $tree->setResource(url("admin/auth/platformMenu"));
        $tree->action(EditAction::class);


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

    public function company()
    {
        $tab = new Tab();
        //子公司TAB
        $companyTable = new TabTable($this->companyModel);
        $companyTable->setTitle("子公司");
        $companyTable->setResourceUrl(url("admin/auth/companies"));
        $companyTable->setBackUrl($this->request->getUri()."&tab=0");
        $companyTable->setCreateHandleParams([
            'parent_id' => $this->mainId
        ]);

        $companyTable->model()->where($this->companyModel->getParentColumn(),$this->mainId);
        $companyTable->column('id',"ID");
        $companyTable->column('name',"名称");
        $tab->add('子公司 ',$companyTable,$this->tab == 0);
        //部门
        $departmentTable = new TabTable($this->departmentModel);
        $departmentTable->setTitle("部门");
        $departmentTable->setResourceUrl(url("admin/auth/departments"));
        $departmentTable->setBackUrl($this->request->getUri()."&tab=1");
        $departmentTable->setCreateHandleParams([
            'company_id' => $this->mainId
        ]);
        $departmentTable->model()->whereHas('company',function($query) {
            $query->where($this->departmentModel->getKeyName(),$this->mainId);
        });
        $departmentTable->column('id',"ID");
        $departmentTable->column('name',"名称");
        $tab->add('部门',$departmentTable,$this->tab == 1);
        return $tab;
    }

    public function department()
    {
        $tab = new Tab();
        //子部门
        $departmentTable = new TabTable($this->departmentModel);
        $departmentTable->setTitle("部门");
        $departmentTable->setResourceUrl(url("admin/auth/departments"));
        $departmentTable->setBackUrl($this->request->getUri()."&tab=0");
        $departmentTable->setCreateHandleParams([
            'parent_id' => $this->mainId
        ]);
        $departmentTable->model()->where($this->departmentModel->getParentColumn(),$this->mainId);
        $departmentTable->column('name',"名称");
        $tab->add('子部门',$departmentTable,$this->tab == 0);
        //用户
        $userTable = new TabTable($this->userModel);
        $userTable->setTitle("用户");
        $userTable->setResourceUrl(url("admin/auth/users"));
        $userTable->setBackUrl($this->request->getUri()."&tab=1");
        $userTable->setCreateHandleParams([
            'department_id' => $this->mainId
        ]);
        $userTable->model()->whereHas('departments',function($query) {
            $query->where('department_id',$this->mainId);
        });
        $userTable->column('name',"名称");
        $tab->add('用户',$userTable,$this->tab == 1);

        return $tab;
    }

    public function user()
    {
        $show = new Show($this->dutyModel->findOrFail($this->mainId));
        $show->setBackUrl($this->request->getUri());
        $show->fields([
            'id' => '职权ID',
            'department.name' => '部门名称',
        ]);
        $show->field('department_type','部门类型')->using(DepartmentType::$text);
        $show->user('用户信息',function ($user) {
            $user->setResource('/admin/auth/users');
            $user->setBackUrl($this->request->getUri());
            $user->id('ID');
            $user->name(trans('admin.name'));
            $user->avatar(trans('admin.avatar'))->image();
            $user->created_at();
            $user->updated_at();
            $user->panel()->tools(function ($tools) {
                $tools->disableEdit(false);
                $tools->disableList();
                $tools->disableDelete();
            });
        });
        $show->roles('职权角色',function (TabTable $roleTable) {
            $roleTable->column('id','ID');
            $roleTable->column('name','名称');
            $roleTable->disableCreateButton();
            $roleTable->disableActions();
            return $roleTable;
        });
        $show->setResource('/admin/auth/duties');
        $show->panel()
            ->title('职权')->tools(function ($tools) {
                $tools->disableEdit(false);
                $tools->disableList();
                $tools->disableDelete();
            });
        return $show;
    }

    protected function getBackUrl()
    {
        $backUrl = session()->pull('user_back_url');
        $backUrl = base64_decode($backUrl);
        if (!$backUrl) $backUrl = url('admin/auth/organizations');
        return $backUrl;
    }


    public function getCompanyList(Request $request)
    {
        $platformId = $request->get('q');
        return $this->companyModel->where('platform_id',$platformId)->get(['id',DB::raw('name as text')]);
    }

    public function getDepartmentList(Request $request)
    {
        $companyId = $request->get('q');
        return $this->departmentModel->where('company_id',$companyId)->get(['id',DB::raw('name as text')]);
    }
}
