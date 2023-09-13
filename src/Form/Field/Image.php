<?php

namespace Encore\OrgRbac\Form\Field;

use Encore\Admin\Form\Field\Image AS BaseImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Image extends BaseImage
{
    /**
     * @param array|UploadedFile $image
     *
     * @return string
     */
    public function prepare($image)
    {
        if (is_null($image)) {
            return $image;
        }
        return parent::prepare($image);
    }
}
