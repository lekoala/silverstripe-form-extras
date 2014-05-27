<?php

/**
 * FormActionConfirm
 *
 * @author lekoala
 */
class FormActionUnvalidated extends FormAction {
	public function __construct($action, $title = "", $form = null) {
		parent::__construct($action, $title, null, $form);
		$this->setAttribute('data-novalidation', 'true');
	}
}
