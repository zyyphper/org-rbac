<?php

namespace Encore\OrgRbac\Show;

use Encore\Admin\Show\Relation AS BaseRelation;
use Encore\OrgRbac\Show;
use Encore\OrgRbac\TabTable\TabTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Relation extends BaseRelation
{
    /**
     * Render this relation panel.
     *
     * @return string
     */
    public function render()
    {
        $relation = $this->model->{$this->name}();

        $renderable = $this->getNullRenderable();

        if ($relation    instanceof HasOne
            || $relation instanceof BelongsTo
            || $relation instanceof MorphOne
        ) {
            $model = $this->model->{$this->name};

            if (!$model instanceof Model) {
                $model = $relation->getRelated();
            }

            $renderable = new Show($model, $this->builder);

            $renderable->panel()->title($this->title);
        }

        if ($relation    instanceof HasMany
            || $relation instanceof MorphMany
            || $relation instanceof BelongsToMany
            || $relation instanceof HasManyThrough
        ) {
            $builder = $this->builder;
            $renderable = new TabTable($relation->getRelated());
            $renderable = $builder($renderable);
            $renderable->setRelation($relation);
            $renderable->setName($this->name)
                ->setTitle($this->title)
                ->setRelation($relation);
        }

        return $renderable->render();
    }
}
