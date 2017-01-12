<?php

/**
 * ComboField
 *
 * @author lekoala
 */
class ComboField extends DropdownField
{

    protected $freeTextItem;
    protected $otherField;
    protected $otherValue = 'OTHER';

    public function __construct($name, $title = null, $source = array(), $value = '', $form = null, $emptyString = null)
    {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);

        if ($emptyString) {
            $this->freeTextItem = $emptyString;
        } else {
            $this->freeTextItem = _t('ComboField.FREETEXT', 'Enter a new value');
        }
        FormExtraJquery::include_jquery();
        Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/ComboField.js');
    }

    public function extraClass()
    {
        return parent::extraClass() . ' dropdown';
    }

    public function setCustomValue($v)
    {
        Session::set('ComboField.' . $this->getName(), $v);
        Session::save();
    }

    public function getCustomValue()
    {
        return Session::get('ComboField.' . $this->getName());
    }

    public function getOtherField()
    {
        return $this->otherField;
    }

    public function setOtherField($otherField)
    {
        $this->otherField = $otherField;
        return $this;
    }

    public function getOtherValue()
    {
        return $this->otherValue;
    }

    public function setOtherValue($otherValue)
    {
        $this->otherValue = $otherValue;
        return $this;
    }

    public function setValue($value)
    {
        if ($value) {
            if (!in_array($value, $this->source)) {
                $this->setCustomValue($value);
            }
        }
        return parent::setValue($value);
    }

    public function saveInto(\DataObjectInterface $record)
    {
        $value = $this->value;
        if ($value) {
            // We have a custom value that needs to be saved to other
            if (!in_array($value, $this->source) && $this->otherField) {
                $record->setCastedField($this->otherField, $this->getCustomValue());
                $this->value = $this->otherValue;
            }
        }

        return parent::saveInto($record);
    }

    public function getSource()
    {
        $source = parent::getSource();
        $v = $this->getCustomValue();
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
        if (empty($this->source) || count($this->source) === 1) {
            $this->setEmptyString('');
        }
        return parent::Field($properties);
    }
}
