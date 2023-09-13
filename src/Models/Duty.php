<?php

namespace Encore\OrgRbac\Models;


use Encore\OrgRbac\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Model;

class Duty extends Model
{
    use HasPermissions;
    protected $fillable = ['user_id','department_id','department_type'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('org.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('org.database.duties_table'));
        parent::__construct($attributes);
    }

    public function user()
    {
        $userModel = config('org.database.users_model');
        return $this->hasOne($userModel,'id','user_id');
    }

    public function userInfo()
    {
        $userModel = config('org.database.users_model');
        $userInfoModel = config('org.database.user_infos_model');
        return $this->hasOneThrough($userInfoModel,$userModel,'id','user_id','user_id','id');
    }

    public function department()
    {
        $department = config('org.database.departments_model');
        return $this->hasOne($department,'id','department_id');
    }

    public function roles()
    {
        $pivotTable = config('org.database.role_duty_table');
        $relatedModel = config('org.database.roles_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'duty_id', 'role_id');
    }

}
