<?php

/**
 * YesNoOptionsetField
 *
 * Boolean defaults to 0, meaning the loadDataFrom dataobject will always
 * set default to "NO" on a Yes/No field because there is no null state
 *
 * To circumvent this, it's easier to store an Enum('YES,NO','NO') in the
 * db
 *
 * @author Koala
 */
class YesNoOptionsetField extends OptionsetField
{
    const VALUE_YES = 'YES';
    const VALUE_NO  = 'NO';

    function __construct($name, $title = null, $source = array(), $value = '',
                         $form = null, $emptyString = null)
    {
        if (empty($source)) {
            $source = array(
                self::VALUE_YES => _t('YesNoOptionsetField.YES', 'Yes'),
                self::VALUE_NO => _t('YesNoOptionsetField.NO', 'No'),
            );
        }
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
    }

    function saveInto(\DataObjectInterface $record)
    {
        if ($this->name) {
            $castingHelper = $record->castingHelper($this->name);
            if ($castingHelper == 'Boolean') {
                $record->setCastedField($this->name, $this->getBooleanValue());
            } else {
                $record->setCastedField($this->name, $this->dataValue());
            }
        }
    }

    function getBooleanValue()
    {
        switch ($this->value) {
            case self::VALUE_NO:
                return false;
            case self::VALUE_YES:
                return true;
        }
    }

    function setYes()
    {
        return $this->setValue(self::VALUE_YES);
    }

    function setNo()
    {
        return $this->setValue(self::VALUE_NO);
    }

    function setValue($value)
    {
        // Avoid setting blank as no
        if ($value === '' || $value === null) {
            return;
        }
        if ($value !== self::VALUE_YES && $value !== self::VALUE_NO) {
            if ((int) $value) {
                $value = self::VALUE_YES;
            } else {
                $value = self::VALUE_NO;
            }
        }
        parent::setValue($value);
    }

    function extraClass()
    {
        return 'optionset '.parent::extraClass();
    }
}