<?php

namespace Encore\OrgRbac\Form;

use Encore\OrgRbac\Form;
use Illuminate\Support\Collection;
use Encore\Admin\Form\Tab AS BaseTab;

class Tab extends BaseTab
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * Tab constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;

        $this->tabs = new Collection();
    }
}
