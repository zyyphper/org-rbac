<?php

namespace Encore\OrgRbac\TabTable\Displayers;

use Encore\Admin\Actions\RowAction;
use Encore\Admin\Table\Displayers\DropdownActions AS BaseDropdownActions;
use Encore\OrgRbac\TabTable\Actions\Delete;
use Encore\OrgRbac\TabTable\Actions\Edit;
use Encore\OrgRbac\TabTable\Actions\EditModal;
use Encore\OrgRbac\TabTable\Actions\View;
use Illuminate\Support\Arr;

class DropdownActions extends BaseDropdownActions
{
    protected $resourceUrl;
    protected $backUrl;
    /**
     * @var array
     */
    protected $defaultClass = [Edit::class, View::class, Delete::class];

    public function setResourceUrl($resourceUrl)
    {
        $this->resourceUrl = $resourceUrl;
    }

    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }

    public function setBackUrl($backUrl)
    {
        $this->backUrl = $backUrl;
    }

    public function getBackUrl()
    {
        return $this->backUrl;
    }

    /**
     * Disable view action.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableView(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->defaultClass, View::class);
        } elseif (!in_array(View::class, $this->defaultClass)) {
            array_push($this->defaultClass, View::class);
        }

        return $this;
    }

    /**
     * Disable delete.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableDelete(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->defaultClass, Delete::class);
        } elseif (!in_array(Delete::class, $this->defaultClass)) {
            array_push($this->defaultClass, Delete::class);
        }

        return $this;
    }

    /**
     * Disable edit.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableEdit(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->defaultClass, Edit::class);
        } elseif (!in_array(Edit::class, $this->defaultClass)) {
            array_push($this->defaultClass, Edit::class);
        }

        return $this;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function dblclick(string $action)
    {
        $this->dblclick = Arr::get([
            'edit'      => Edit::class,
            'view'      => View::class,
            'delete'    => Delete::class,
            'select'    => 'select',
        ], $action);

        return $this;
    }

    /**
     * Prepend default `edit` `view` `delete` actions.
     */
    protected function prependDefaultActions()
    {
        foreach ($this->defaultClass as $class) {
            if ($this->table->modalForm && $class == Edit::class) {
                $class = EditModal::class;
            }
            /** @var RowAction $action */
            $action = new $class($this->resourceUrl,$this->backUrl);

            $this->prepareAction($action);

            array_push($this->default, $action);
        }
    }
}
