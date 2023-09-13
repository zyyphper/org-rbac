<?php

namespace Encore\OrgRbac\Form;


use Encore\OrgRbac\Form;
use Illuminate\Support\Collection;
use Encore\Admin\Form\Builder AS BaseBuilder;

/**
 * Class Builder.
 */
class Builder extends BaseBuilder
{
    /**
     * Builder constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;

        $this->fields = new Collection();

        $this->init();
    }

}
