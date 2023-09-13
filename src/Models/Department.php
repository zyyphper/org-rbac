<?php

namespace Encore\OrgRbac\Models;


use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['id','parent_id','company_id','name','leader','order'];

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
        $this->setTable(config('org.database.departments_table'));
        parent::__construct($attributes);
    }

    public function company()
    {
        $companyModel = config('org.database.companies_model');
        return $this->belongsTo($companyModel,'company_id');
    }

    public function users()
    {
        $pivotTable = config('org.database.duties_model');
        $relatedModel = config('org.database.users_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'department_id', 'user_id');
    }

    public function duties()
    {
        $dutyModel = config('org.database.duties_model');
        return $this->hasMany($dutyModel,'department_id');
    }
}
