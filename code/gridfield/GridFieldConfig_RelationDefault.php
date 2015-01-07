<?php

/**
 * GridFieldConfig_RelationDefault
 *
 * @author lekoala
 */
class GridFieldConfig_RelationDefault extends GridFieldConfig_RelationEditor
{

    public function __construct($itemsPerPage = null, $sort = false)
    {
        parent::__construct($itemsPerPage);

        if ($sort) {
            if (class_exists('GridFieldSortableRows')) {
                $this->addComponent(new GridFieldSortableRows('SortOrder'));
            } else if (class_exists('GridFieldOrderableRows')) {
                $this->addComponent(new GridFieldOrderableRows('SortOrder'));
            }
        }
        if (class_exists('GridFieldBulkManager')) {
            $this->addComponent(new GridFieldBulkManager());
        }
    }
}