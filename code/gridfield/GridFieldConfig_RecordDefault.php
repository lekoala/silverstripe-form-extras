<?php

/**
 * GridFieldConfig_RecordDefault
 *
 * @author lekoala
 */
class GridFieldConfig_RecordDefault extends GridFieldConfig_RecordEditor
{

    public function __construct($itemsPerPage = null, $sort = true)
    {
        parent::__construct($itemsPerPage);

        if ($sort) {
            if (class_exists('GridFieldSortableRows')) {
                $this->addComponent(new GridFieldSortableRows('SortOrder'));
            } elseif (class_exists('GridFieldOrderableRows')) {
                $this->addComponent(new GridFieldOrderableRows('SortOrder'));
            }
        }
        if (class_exists('GridFieldBulkManager')) {
            $this->addComponent(new GridFieldBulkManager());
        }
    }
}
