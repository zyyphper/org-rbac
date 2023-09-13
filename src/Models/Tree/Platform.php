<?php

namespace Encore\OrgRbac\Models\Tree;


use Encore\Admin\Facades\Admin;
use Encore\OrgRbac\Traits\ModelRelationTree;
use Encore\OrgRbac\Models\Platform AS BaseModel;
use Illuminate\Support\Facades\DB;

class Platform extends BaseModel
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
        $relationTreeModel = config('org.database.companies_tree');
        $this->setTitleColumn('name');
        $this->setRelationModelTree($relationTreeModel,'platform_id');
    }

    public function allNodes()
    {
        $connection = config('org.database.connection') ?: config('database.default');
        $orderColumn = DB::connection($connection)->getQueryGrammar()->wrap($this->orderColumn);
        $query = static::query();

        if (!Admin::user()->isRootAdministrator()) {
            $query = $query->where($this->getKeyName(),Admin::user()->platform_id);
        }
        return $query->selectRaw('*'.",0 parent_id")
            ->get()->toArray();
    }
}
