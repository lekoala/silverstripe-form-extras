<?php

/**
 * A currency field with some default behaviours
 *
 * @author Koala
 */
class MaskedCurrencyField extends MaskedInputField
{

    public function __construct($name, $title = null, $value = '',
                                $maxLength = null, $form = null)
    {
        parent::__construct($name, $title, $value, $maxLength, $form);
        $this->setAlias(MaskedInputField::ALIAS_DECIMAL);
        $this->setDigits(2);
        $this->setRightAlign(false);
    }

    public function setValue($value)
    {
        $value = (float) $value;
        return parent::setValue($value);
    }
}