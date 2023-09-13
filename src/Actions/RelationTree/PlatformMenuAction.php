<?php

namespace Encore\OrgRbac\Actions\RelationTree;

class PlatformMenuAction extends Action
{
    protected $name = "platformMenu";
    protected $buttonURI = "platform_menu";

    public function html($branch)
    {
        $url = url("/admin/auth/organizations")."?action=$this->name&type=$this->type&main_id=".$branch[$this->keyName];
        return <<<HTML
        <a href="$url"><i class="fab fa-microsoft" title="菜单"></i></a>
HTML;
    }
}
