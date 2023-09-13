<?php

namespace Encore\OrgRbac\Models;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Org extends Model
{
    use \Encore\Admin\Traits\ModelTree;

    /**
     * Build Nested array.
     *
     * @param array $nodes
     * @param int   $parentId
     *
     * @return array
     */
    protected function buildNestedArray(array $nodes = [], $parentId = 0)
    {
        $branch = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $node) {
            if ($node[$this->getParentColumn()] == $parentId) {
                $children = $this->buildNestedArray($nodes, $node[$this->getKeyName()]);

                if ($children) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }

    /**
     * Get all elements.
     *
     * @return mixed
     */
    public function allNodes()
    {
        $self = new static();

        if ($this->queryCallback instanceof \Closure) {
            $self = call_user_func($this->queryCallback, $self);
        }

        if (property_exists($this, 'orderColumn')) {
            $orderColumn = DB::getQueryGrammar()->wrap($this->getOrderColumn());
            $byOrder = $orderColumn.' = 0,'.$orderColumn;

            return $self->orderByRaw($byOrder)->get()->toArray();
        }

        return $self->get()->toArray();
    }

}
