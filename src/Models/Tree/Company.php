<?php

namespace Encore\OrgRbac\Models\Tree;


use Encore\OrgRbac\Traits\ModelRelationTree;
use Encore\OrgRbac\Models\Company AS BaseModel;

class Company extends BaseModel
{
    use ModelRelationTree{
        ModelRelationTree::boot as treeBoot;
    }

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $relationTreeModel = config('org.database.departments_tree');
        $this->setTitleColumn('name');
        $this->setRelationModelTree($relationTreeModel,'company_id');
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        static::treeBoot();
    }
}
