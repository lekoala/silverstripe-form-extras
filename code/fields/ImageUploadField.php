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
        $this->getValidator()->setAllowedExtensions(
            self::config()->default_allowed_extensions
        );
        $sizeInBytes = self::config()->default_max_file_size * 1024 * 1024;
        $this->getValidator()->setAllowedMaxFileSize($sizeInBytes);
    }
}