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

    public function __construct($name, $title = null, $source = array(), $value = '',
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

    public function saveInto(\DataObjectInterface $record)
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

    public function getBooleanValue()
    {
        switch ($this->value) {
            case self::VALUE_NO:
                return false;
            case self::VALUE_YES:
                return true;
        }
    }

    public function setYes()
    {
        return $this->setValue(self::VALUE_YES);
    }

    public function setNo()
    {
        return $this->setValue(self::VALUE_NO);
    }

    public function setValue($value)
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
        return parent::setValue($value);
    }

    public function extraClass()
    {
        return 'optionset '.parent::extraClass();
    }
}
