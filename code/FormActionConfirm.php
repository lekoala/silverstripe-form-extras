<?php

/**
 * FormActionConfirm
 *
 * @author lekoala
 */
class FormActionConfirm extends FormAction
{
    protected $confirmText = 'Are you sure?';

    public function __construct($action, $title = "", $form = null)
    {
        $this->confirmText = _t('FormActionConfirm.AREYOUSURE', "Are you sure ?");
        parent::__construct($action, $title, $form);
    }

    public function Field($properties = array())
    {
        return parent::Field($properties);
    }

    public function setConfirmText($v)
    {
        $this->confirmText = $v;
        return $this;
    }

    public function ConfirmText()
    {
        return $this->confirmText;
    }
}