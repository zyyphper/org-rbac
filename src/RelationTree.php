<?php

namespace Encore\OrgRbac;

use Encore\Admin\Admin;
use Encore\Admin\Tree;

class RelationTree extends Tree
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
        'branch' => 'org_rbac::tree.relationTree.branch',
    ];

    public function getResource()
    {
        if ($this->resource) return $this->resource;
        return url(app('request')->getPathInfo());
    }

    public function setResource($resource)
    {
        if (!empty($resource)) {
            $this->resource = $resource;
        }
    }


    public function action($type,$actionClass,$resource = '')
    {
        $action = new $actionClass([
            'type'           => $type,
            'keyName'        => $this->model->getKeyName(),
            'resource'       => $resource ?: $this->getResource()
        ]);
        $this->actions[$type][] = $action;
    }

    public function actions(array $types,$actionClass,$resource = '')
    {
        foreach ($types as $type) {
            $this->action($type,$actionClass,$resource);
        }
    }

    public function render()
    {
        view()->share([
            'path'           => $this->path,
            'actions'        => $this->actions,
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
