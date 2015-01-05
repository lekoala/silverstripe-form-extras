<?php

/**
 * GridFieldConfig_Default
 *
 * @author lekoala
 */
class GridFieldConfig_Default extends GridFieldConfig_RelationEditor {

	public function __construct($itemsPerPage=null, $sort = false) {
		parent::__construct($itemsPerPage);
		
		$this->removeComponentsByType('GridFieldAddExistingAutocompleter');
		$this->removeComponentsByType('GridFieldDeleteAction');
		$this->addComponent(new GridFieldDeleteAction(false));
        if($sort) {
            $this->addComponent(new GridFieldOrderableRows());
        }
	}

}
