<?php

namespace Encore\OrgRbac\TabTable\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Table\Tools\AbstractTool;
use Encore\OrgRbac\TabTable\TabTable;

class CreateButton extends AbstractTool
{
    /**
     * @var TabTable
     */
    protected $table;

    /**
     * @var string
     */
    protected $urlParams;

    /**
     * Create a new CreateButton instance.
     *
     * @param TabTable $table
     * @param array $params
     */
    public function __construct(TabTable $table)
    {
        $params = [];
        if ($table->getBackUrl()) $params = ['back_url'=>base64_encode($table->getBackUrl())];
        $params = array_merge($table->getCreateHandleParams(),$params);
        $this->table = $table;
        !empty($params) && $this->urlParams = http_build_query($params);
    }

    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render()
    {
        if (!$this->table->showCreateBtn()) {
            return '';
        }
//        if (!OrgRbac::permission()->check($this->table->getCreateButtonUri())) {
//            return '';
//        }

        $url = $this->table->getResourceUrl()."/create";
        if ($this->urlParams) $url .= "?".$this->urlParams;

        return Admin::view('admin::table.create-btn', [
            'url'   => $url,
            'modal' => $this->table->modalForm,
        ]);
    }
}
