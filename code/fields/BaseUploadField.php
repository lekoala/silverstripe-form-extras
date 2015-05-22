<?php

/**
 * BaseUploadField
 *
 * Default upload files store everything into /Uploads which is quite annoying
 * This class has a helper method to create an UploadField that stores files
 * in a consistent location
 *
 * @author lekoala
 */
class BaseUploadField extends UploadField
{

    /**
     * Return an instance of UploadField with the folder name already set up
     * 
     * @param object|string $class
     * @param string $name
     * @param string $title
     * @param \SS_List $items
     * @return \static
     */
    public static function createForClass($class, $name, $title = null,
                                          \SS_List $items = null)
    {
        $folderName = self::getFolderForClass($class);

        $inst = new static($name, $title, $items);
        $inst->setFolderName($folderName);
        return $inst;
    }

    /**
     * Get folder for a given class
     * 
     * @param mixed $class
     * @return string
     */
    public static function getFolderForClass($class)
    {
        $folderName = 'Uploads';

        if (is_object($class)) {
            if ($class instanceof Page) {
                $folderName = get_class($class);
            } else if ($class instanceof DataObject) {
                $folderName = $class->baseTable();
            } else if ($class instanceof DataExtension) {
                $folderName = $class->getOwner()->baseTable();
            } else {
                $folderName = get_class($class);
            }
        } else if (is_string($class)) {
            $folderName = $class;
        }

        if (class_exists('Subsite') && Config::inst()->get(__CLASS__,
                'use_subsite_integration')) {
            $subsite = Subsite::currentSubsite();
            if ($subsite) {
                // Subsite extras integration$
                if ($subsite->hasField('BaseFolder')) {
                    $baseFolder = $subsite->BaseFolder;
                } else {
                    $filter     = new URLSegmentFilter();
                    $baseFolder = $filter->filter($subsite->getTitle());
                    $baseFolder = str_replace(' ', '',
                        ucwords(str_replace('-', ' ', $baseFolder)));
                }
                if (!empty($baseFolder)) {
                    $folderName = $baseFolder.'/'.$folderName;
                }
            }
        }

        return $folderName;
    }

    public function setImageOptions($ext = null, $size = null)
    {
        $this->getValidator()->setAllowedExtensions(
            $ext ? $ext : ImageUploadField::config()->default_allowed_extensions
        );
        $sizeInBytes = $size ? $size : ImageUploadField::config()->default_max_file_size
            * 1024 * 1024;
        $this->getValidator()->setAllowedMaxFileSize($sizeInBytes);

        return $this;
    }

    public function __construct($name, $title = null, \SS_List $items = null)
    {
        parent::__construct($name, $title, $items);
    }
}