<?php

namespace Encore\OrgRbac;

use Encore\Admin\Show AS BaseShow;
use Encore\OrgRbac\Show\Panel;
use Encore\OrgRbac\Show\Relation;

class Show extends BaseShow
{
    protected $backUrl;

    public function setBackUrl($backUrl)
    {
        $this->backUrl = $backUrl;
    }

    public function getBackUrl()
    {
        return $this->backUrl;
    }

    /**
     * Initialize panel.
     */
    protected function initPanel()
    {
        $this->panel = new Panel($this);
    }

    /**
     * Add a relation panel to show.
     *
     * @param string   $name
     * @param \Closure $builder
     * @param string   $label
     *
     * @return Relation
     */
    protected function addRelation($name, $builder, $label = '')
    {
        $relation = new Relation($name, $builder, $label);

        $relation->setParent($this);

        $this->overwriteExistingRelation($name);

        return tap($relation, function ($relation) {
            $this->relations->push($relation);
        });
    }
}
