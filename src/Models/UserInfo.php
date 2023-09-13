<?php

namespace Encore\OrgRbac\Models;


use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $fillable = ['user_id','phone','email', 'is_check_identity','realname', 'identity_code'];

    protected $primaryKey = 'user_id';
    public $incrementing = false;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('org.database.connection') ?: config('database.default');
        $this->setConnection($connection);
        $this->setTable(config('org.database.user_infos_table'));
        parent::__construct($attributes);
    }

    public function user()
    {
        $userModel = config('org.database.users_model');
        return $this->hasOne($userModel,'user_id');
    }

}
