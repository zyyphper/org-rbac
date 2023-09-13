<?php

namespace Encore\OrgRbac\Actions\Tree;


class EditAction extends Action
{
    protected $name = "edit";

    public function html($branch)
    {
        $url = $this->getResource()."/".$branch[$this->keyName]."/edit";
        return <<<HTML
        <a href="$url"><i class="fa fa-edit" title="编辑"></i></a>
HTML;
    }

}
