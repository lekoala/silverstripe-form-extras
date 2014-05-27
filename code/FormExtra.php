<?php

/**
 * FormExtra
 *
 * @author lekoala
 */
class FormExtra extends Form {

	public function __construct($controller = null, $name = null, FieldList $fields = null, FieldList $actions = null, $validator = null) {
		if($controller === null) {
			$controller = Controller::curr();
		}
		if($name === null) {
			$name = get_called_class();
		}
		if($fields === null) {
			$fields = new FieldList;
		}
		if($actions === null) {
			$actions = new FieldList;
		}
		parent::__construct($controller, $name, $fields, $actions);
	}
	
	protected function err($msg) {
		$this->sessionMessage($msg, 'bad');
		return $this->Controller()->redirectBack();
	}
}
