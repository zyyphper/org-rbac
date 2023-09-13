<?php

namespace Encore\OrgRbac\Actions\RelationTree;

class DetailAction extends Action
{
    protected $name = "detail";
    protected $buttonURI = "org_tree_detail";

    public function html($branch)
    {
        $url = url("/admin/auth/organizations")."?action=$this->name&type=$this->type&main_id=".$branch[$this->keyName];
        return <<<HTML
        <a href="$url"><i class="fas fa-angle-double-right" title="详情"></i></a>
HTML;
    }
}
