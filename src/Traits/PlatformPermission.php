<?php

namespace Encore\OrgRbac\Traits;


use Encore\Admin\Facades\Admin;
use Encore\Admin\Table;
use Encore\OrgRbac\Form;
use Encore\OrgRbac\Models\Enums\IsAdmin;
use Encore\OrgRbac\TabTable\TabTable;

trait PlatformPermission
{
    protected $platformId;
    protected $companyId;
    protected $data;

    public function model()
    {
        $model = config('org.database.platforms_model');
        return new $model();
    }

    public function getPlatformId()
    {
        if (!empty($this->platformId)) {
            return $this->platformId;
        }
        return Admin::user()->platform_id;
    }

    public function setPlatformIdByDepartmentId($departmentId)
    {
        $departmentModel = config('org.database.departments_model');
        $this->platformId = $departmentModel::find($departmentId)->company->platform->id;
        return $this;
    }

    public function getCompanyId()
    {
        return $this->companyId;
    }

    public function toPluck()
    {
        return $this->data;
    }

    protected function setRootPluck($data) {
        $data[0] = "ROOT";
        return $this;
    }

    public function platformAuth(&$obj)
    {
        if ($obj instanceof Table || $obj instanceof TabTable) {
            if (Admin::user()->isRootAdministrator()) {
                $obj->platform()->name("平台");
                return;
            }
            $obj->model()->where('platform_id',$this->getPlatformId());
            return;
        }
        if ($obj instanceof Form) {
            if (Admin::user()->isRootAdministrator()) {
                $obj->select('platform_id','平台')->options(
                    $this->model()->where('is_admin',IsAdmin::NO)->get()->pluck('name','id')
                );
                return;
            }
            $obj->select('platform_id','平台')->options(
                $this->model()->all()->pluck('name','id')
            )->value($this->getPlatformId())->disable();
        }

    }


    public function getCompany()
    {
        $companyModel = config('org.database.companies_model');
        $this->data = $companyModel::where('platform_id',$this->getPlatformId())->pluck('name','id');
        return $this;
    }

    public function getDepartment()
    {
        $departmentModel = config('org.database.departments_model');
        $departmentModel = new $departmentModel();
        if ($this->companyId) {
            $departmentModel = $departmentModel->where('company_id',$this->getCompanyId());
        } else {
            $departmentModel = $departmentModel->with(['company'=>function ($query) {
                $query->where('platform_id',$this->getPlatformId());
            }]);
        }
        $this->data = $departmentModel->pluck('name','id');
        return $this;
    }

    public function getTreeToCompanyAndDepartment()
    {
        $companyData = $this->getCompany()->formatDataToTree();
        foreach ($companyData as &$data) {
            $this->companyId = $data['value'];
            $data['children'] = $this->getDepartment()->formatDataToTree();
        }

        return $companyData;
    }

    public function formatDataToTree()
    {
        $result = [];
        foreach ($this->data as $id => $name) {
            array_push($result,[
                'text' => $name,
                'value' => "$id",
            ]);
        }
        return $result;
    }

}
