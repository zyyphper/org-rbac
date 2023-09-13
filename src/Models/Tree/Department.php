<?php

namespace Encore\OrgRbac\Models\Tree;


use Encore\OrgRbac\Traits\ModelRelationTree;
use Encore\OrgRbac\Models\Department AS BaseModel;

class Department extends BaseModel
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
        $relationTreeModel = config('org.database.users_tree');
        $this->setTitleColumn('name');
        $this->setRelationModelTree($relationTreeModel,'department_id');
    }

}
