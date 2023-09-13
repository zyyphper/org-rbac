<?php

namespace Encore\OrgRbac\Models;


use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $fillable = ['id','name', 'status'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('org.database.connection') ?: config('database.default');
        $this->setConnection($connection);
        $this->setTable(config('org.database.platforms_table'));
        parent::__construct($attributes);
    }


    public function companies()
    {
        $companyModel = config('org.database.companies_model');
        return $this->hasMany($companyModel);
    }
}
