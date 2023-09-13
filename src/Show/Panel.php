<?php

namespace Encore\OrgRbac\Show;

use Encore\Admin\Show\Panel AS BasePanel;
use Illuminate\Support\Collection;

class Panel extends BasePanel
{
    /**
     * Initialize view data.
     */
    protected function initData()
    {
        $this->data = [
            'fields' => new Collection(),
            'tools'  => new Tools($this->getParent()),
            'style'  => 'info',
            'title'  => trans('admin.detail'),
        ];
    }
}
