<?php

namespace Encore\OrgRbac\TabTable\Actions;

use Encore\Admin\Actions\RowAction;


class Edit extends RowAction
{
    protected $resourceUrl;
    protected $backUrl;

    public function __construct($resourceUrl,$backUrl = '')
    {
        $this->resourceUrl = $resourceUrl;
        $this->backUrl = $backUrl;
    }

    public $name = 'Edit';

    /**
     * @return string
     */
    public function href()
    {
        $href = "{$this->resourceUrl}/{$this->getKey()}/edit";
        if (!empty($this->backUrl)) {
            $href .= "?back_url=".base64_encode($this->backUrl);
        }
        return $href;
    }
}
