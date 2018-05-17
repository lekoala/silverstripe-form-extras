<?php

require_once 'Zend/Locale/Data.php';

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

        // Some locale use "," as radix
        $locale  = i18n::get_locale();
        $symbols = Zend_Locale_Data::getList($locale, 'symbols');
        if (!empty($symbols) && $symbols['decimal'] == ',') {
            $this->setRadixPoint(',');
        }
    }

    public function setValue($value)
    {
        // Just make sure that whatever value we pass in set in correct locale
        if ($this->getRadixPoint() == '.') {
            $value = str_replace(',', '.', $value);
        } else {
            $value = str_replace('.', ',', $value);
        }
        return parent::setValue($value);
    }

    public function dataValue()
    {
        // Data value should always be a valid number to be savec in the database
        return str_replace(',', '.', parent::dataValue());
    }
}
