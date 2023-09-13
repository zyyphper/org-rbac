<?php
/**
 * Copyright (c) 2019. Mallto.Co.Ltd.<mall-to.com> All rights reserved.
 */

namespace Encore\OrgRbac\Form\Layout;

use Encore\OrgRbac\Form;
use Illuminate\Support\Collection;
use Encore\Admin\Form\Layout\Layout AS BaseLayout;

class Layout extends BaseLayout
{
    /**
     * @var Form
     */
    protected $parent;

    /**
     * Layout constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->parent = $form;

        $this->current = new Column();

        $this->columns = new Collection();
    }


}
