<?php

/**
 * FormActionConfirm
 *
 * @author lekoala
 */
class FormActionConfirm extends FormAction {

	protected $confirmText = 'Are you sure?';
	
	public function setConfirmText($v) {
		$this->confirmText = $v;
		return $this;
	}

	public function ConfirmText() {
		return $this->confirmText;
	}

}
