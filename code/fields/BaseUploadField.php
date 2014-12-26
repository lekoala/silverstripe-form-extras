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
        $folderName = 'Uploads';

        if (is_object($class)) {
            if ($class instanceof DataObject) {
                $folderName = $class->baseTable();
            } else if ($class instanceof DataExtension) {
                $folderName = $class->getOwner()->baseTable();
            } else {
                $folderName = get_class($class);
            }
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

        $inst = new static($name, $title, $items);
        $inst->setFolderName($folderName);
        return $inst;
    }

    public function __construct($name, $title = null, \SS_List $items = null)
    {
        parent::__construct($name, $title, $items);
    }
}