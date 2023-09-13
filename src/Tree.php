<?php

namespace Encore\OrgRbac;

use Encore\Admin\Admin;
use Encore\Admin\Tree AS BaseTree;

class Tree extends BaseTree
{
    protected $actions;
    protected $resource;
    /**
     * View of tree to render.
     *
     * @var string
     */
    protected $view = [
        'tree'   => 'org_rbac::tree.tree',
        'branch' => 'org_rbac::tree.branch',
    ];

    public function getResource()
    {
        if ($this->resource) return $this->resource;
        return url(app('request')->getPathInfo());
    }

    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    public function action($actionClass,$resource = '')
    {
        $this->actions[] = [
            $actionClass,
            $resource
        ];
    }

    public function actions(array $actionClass,$resource = '')
    {
        foreach ($actionClass as $action) {
            $this->action($action,$resource);
        }
    }

    public function actionInit()
    {
        $action = [];
        foreach ($this->actions as $actionClass) {
            $action[] = new $actionClass[0]([
                'keyName'        => $this->model->getKeyName(),
                'resource'       => $actionClass[1] ?: $this->getResource()
            ]);
        }
        return $action;
    }

    public function render()
    {
        view()->share([
            'path'           => $this->path,
            'actions'        => $this->actionInit(),
            'keyName'        => $this->model->getKeyName(),
            'branchView'     => $this->view['branch'],
            'branchCallback' => $this->branchCallback,
            'model'          => get_class($this->model),
        ]);

        return Admin::view($this->view['tree'], [
            'id'         => $this->elementId,
            'tools'      => $this->tools->render(),
            'items'      => $this->getItems(),
            'useCreate'  => $this->useCreate,
            'useSave'    => $this->useSave,
            'url'        => url($this->path),
            'options'    => $this->options,
        ]);
    }

    /**
     * Set view of tree.
     *
     * @param array $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }
}
