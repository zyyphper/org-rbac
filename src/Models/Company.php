<?php

namespace Encore\OrgRbac\Models;


use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['id','parent_id','platform_id','name','email','phone','order'];

    protected $primaryKey = 'id';
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
        $this->setTable(config('org.database.companies_table'));
        parent::__construct($attributes);
    }


    public function platform()
    {
        $platformModel = config('org.database.platforms_model');
        return $this->belongsTo($platformModel,'platform_id');
    }

    public function departments()
    {
        $departmentModel = config('org.database.departments_model');
        return $this->hasMany($departmentModel);
    }
}
