<?php

namespace Encore\OrgRbac\Form\Field;

use Encore\Admin\Form\Field;
use Encore\Admin\Form\Field\HasMany AS BaseHasMany;
use Encore\OrgRbac\Form\NestedForm;
use Illuminate\Support\Arr;

/**
 * Class HasMany.
 */
class HasMany extends BaseHasMany
{
    /**
     * Get validator for this field.
     *
     * @param array $input
     *
     * @return bool|\Illuminate\Contracts\Validation\Validator
     */
    public function getValidator(array $input)
    {
        if (!array_key_exists($this->column, $input)) {
            return false;
        }

        $input = Arr::only($input, $this->column);

        /** unset item that contains remove flag */
        foreach ($input[$this->column] as $key => $value) {
            if ($value[NestedForm::REMOVE_FLAG_NAME]) {
                unset($input[$this->column][$key]);
            }
        }

        $form = $this->buildNestedForm($this->column, $this->builder);

        $rules = $attributes = [];

        /* @var Field $field */
        foreach ($form->fields() as $field) {
            if (!$fieldRules = $field->getRules()) {
                continue;
            }

            $column = $field->column();

            // daterange or map field etc..
            if (is_array($column)) {
                foreach ($column as $name) {
                    $rules[$name] = $fieldRules;
                }
            } else {
                $rules[$column] = $fieldRules;
            }

            $attributes = array_merge(
                $attributes,
                $this->formatValidationAttribute($input, $field->label(), $column)
            );
        }

        Arr::forget($rules, NestedForm::REMOVE_FLAG_NAME);

        if (empty($rules)) {
            return false;
        }

        $newRules = [];

        foreach ($rules as $key => $rule) {
            $newRules["{$this->column}.*.{$key}"] = $rule;
        }

        $this->appendDistinctRules($newRules);

        return \validator($input, $newRules, $this->getValidationMessages(), $attributes);
    }

    /**
     * Build a Nested form.
     *
     * @param string   $column
     * @param \Closure $builder
     * @param null     $model
     *
     * @return NestedForm
     */
    protected function buildNestedForm($column, \Closure $builder, $model = null)
    {
        $form = new NestedForm($column, $model);

        $form->setForm($this->form);

        call_user_func($builder, $form);

        $form->hidden($this->getKeyName());
        $form->hidden(NestedForm::REMOVE_FLAG_NAME)
            ->default(0)
            ->addElementClass(NestedForm::REMOVE_FLAG_CLASS);

        return $form;
    }
}
