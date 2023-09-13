<?php

namespace Encore\OrgRbac\Form\Field;

use Encore\Admin\Form\Field\Select AS BaseSelect;


class SecondSelect extends BaseSelect
{
    protected $view = "org_rbac::form.secondSelect";

    /**
     * Load right options for left select on change.
     *
     * @param array $data
     *
     * @return $this
     */
    public function loadData($data)
    {
        $class = $this->column;
        $data = json_encode($data);

        return $this->addVariables(['loadData' => compact('class', 'data')]);
    }
}
