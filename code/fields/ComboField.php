<?php

/**
 * ComboField
 *
 * @author lekoala
 */
class ComboField extends DropdownField
{
    protected $freeTextItem;

    public function __construct($name, $title = null, $source = array(),
                                $value = '', $form = null, $emptyString = null)
    {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);

        if ($emptyString) {
            $this->freeTextItem = $emptyString;
        } else {
            $this->freeTextItem = _t('ComboField.FREETEXT', 'Enter a new value');
        }
        FormExtraJquery::include_jquery();
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/ComboField.js');
    }

    function extraClass()
    {
        return parent::extraClass().' dropdown';
    }

    public function setCustomValue($v)
    {
        Session::set('ComboField.'.$this->getName(), $v);
        Session::save();
    }

    public function getCustomValue()
    {
        return Session::get('ComboField.'.$this->getName());
    }

    public function getSource()
    {
        $source = parent::getSource();
        $v      = $this->getCustomValue();
        if ($v) {
            $source[$v] = $v;
        }
        $v = $this->getFreeTextItem();
        if ($v) {
            $source['_'] = $v;
        }
        return $source;
    }

    public function getFreeTextItem()
    {
        return $this->freeTextItem;
    }

    public function setFreeTextItem($freeTextItem)
    {
        $this->freeTextItem = $freeTextItem;
    }

    public function Field($properties = array())
    {
        return parent::Field($properties);
    }
}