<?php

namespace Encore\OrgRbac\Models;


use Encore\Admin\Models\Administrator;
use Encore\OrgRbac\Models\Enums\DepartmentType;
use Encore\OrgRbac\Traits\HasPermissions;

class User extends Administrator
{
    use HasPermissions;
    protected $fillable = ['id','platform_id','username', 'password','name', 'is_admin'];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public function departments()
    {
        $pivotTable = config('org.database.duties_table');
        $relatedModel = config('org.database.departments_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'department_id')->withPivot('id');
    }

    public function mainDuty()
    {
        return $this->duties()->where('department_type',DepartmentType::MAIN)->first();
    }

    public function duties()
    {
        $dutyModel = config('org.database.duties_model');
        return $this->hasMany($dutyModel,'user_id');
    }

    public function rolesByDuty()
    {
        $dutyId = \Encore\OrgRbac\Duty\Duty::load()->getId();
        $dutyModel = config('org.database.duties_model');
        return $dutyModel::find($dutyId)->roles();
    }

    public function buttonPermissionsByDuty()
    {
        $idArray = $this->rolesByDuty()->pluck('id')->toArray();
        $menuModel = config('org.database.menu_model');
        return $menuModel::whereHas('roles',function($query) use($idArray) {
            $query->whereIn('role_id',$idArray);
        })->get();
    }

    public function info()
    {
        $userInfoModel = config('org.database.user_infos_model');
        return $this->hasOne($userInfoModel,'user_id');
    }

}
