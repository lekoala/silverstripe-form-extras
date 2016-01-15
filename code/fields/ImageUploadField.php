<?php

/**
 * ImageUploadField
 *
 * @author lekoala
 */
class ImageUploadField extends BaseUploadField
{

    public function __construct($name, $title = null, \SS_List $items = null)
    {
        parent::__construct($name, $title, $items);
        $this->setImageOptions();
    }
}
