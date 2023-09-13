<?php

namespace Encore\OrgRbac\Layout;

use Encore\Admin\Layout\Content AS BaseContent;

class Content extends BaseContent
{
    /**
     * Render this content.
     *
     * @return string
     */
    public function render()
    {
        $items = [
            'header'      => $this->title,
            'description' => $this->description,
            'breadcrumb'  => $this->breadcrumb,
            '__content'   => $this->build(),
            '__view'      => $this->view,
        ];

        return view('org_rbac::content', $items)->render();
    }
}
