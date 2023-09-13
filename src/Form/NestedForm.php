<?php

namespace Encore\OrgRbac\Form;

use Encore\Admin\Form\NestedForm AS BaseNestedForm;
use Encore\OrgRbac\Form;
use Illuminate\Support\Arr;

/**
 * Class NestedForm
 * @method Field\SecondSelect secondSelect
 * @package Encore\OrgRbac\Form
 */
class NestedForm extends BaseNestedForm
{
    /**
     * Add nested-form fields dynamically.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ($className = Form::findFieldClass($method)) {
            $column = Arr::get($arguments, 0, '');

            /* @var Field $field */
            $field = new $className($column, array_slice($arguments, 1));

            $field->setForm($this)->setNested();

            return tap($this->formatField($field), function ($field) {
                $this->pushField($field);
            });
        }

        return $this;
    }
}
