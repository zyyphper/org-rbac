<?php

namespace Encore\OrgRbac\Show;

use Encore\Admin\Show\Actions\Edit;
use Encore\Admin\Show\Tools AS BaseTools;
use Encore\OrgRbac\Show;
use Illuminate\Support\Collection;

class Tools extends BaseTools
{
    /**
     * Tools constructor.
     *
     * @param Show $show
     */
    public function __construct(Show $show)
    {
        $this->show = $show;
        $this->default = new Collection();
        $this->appends = new Collection();
        $this->prepends = new Collection();
    }

    /**
     * Get request path for edit.
     *
     * @return string
     */
    protected function getEditPath()
    {
        $key = $this->show->getModel()->getKey();
        $backUrl = $this->show->getBackUrl();
        $url = $this->getListPath().'/'.$key.'/edit';
        if ($backUrl) return $url ."?back_url=".base64_encode($backUrl);
        return $url;
    }

    /**
     * Render tools.
     *
     * @return string
     */
    public function render()
    {
        $output = $this->renderCustomTools($this->prepends);
        foreach ($this->default as $method => $tool) {
            $output .= $tool->render();
        }

        return $output.$this->renderCustomTools($this->appends);
    }
}
