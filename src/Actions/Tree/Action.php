<?php

namespace Encore\OrgRbac\Actions\Tree;

use Encore\Admin\Facades\Admin;

class Action
{
    protected $keyName;
    protected $name;
    protected $resource;
    protected $buttonURI = '';

    public function __construct($options)
    {
        $this->keyName = $options['keyName'];
        $this->setResource($options['resource']);
    }

    public function getResource()
    {
        if ($this->resource) return $this->resource;
        return url(app('request')->getPathInfo());
    }

    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    public function render($branch)
    {
        if (Admin::user()->cannot($this->buttonURI)) return "";
        return $this->html($branch);
    }

    public function html($branch){
        return "";
    }
}
