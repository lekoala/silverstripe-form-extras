<?php

/**
 * A field that uses accounting for formatting
 *
 * @author Koala
 */
class AccountingField extends TextField
{

    public function extraClass()
    {
        return 'text '.parent::extraClass();
    }

    public function Field($properties = array())
    {
        FormExtraJquery::include_jquery();
        FormExtraJquery::include_accounting();
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/AccountingField.js');

        return parent::Field($properties);
    }

    public function getPrecision()
    {
        return $this->getAttribute('data-precision');
    }

    public function setPrecision($value)
    {
        return $this->setAttribute('data-precision', $value);
    }

    public function dataValue()
    {
        return self::unformat($this->value);
    }

    /**
     * Similar implementation than accounting.js unformat method
     * @param string $value
     */
    public static function unformat($value)
    {
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = self::unformat($val);
            }
            return $value;
        }

        if (!$value) {
            $value = 0;
        }

        $cleanString       = preg_replace('/([^0-9\.,])/i', '', $value);
        $onlyNumbersString = preg_replace('/([^0-9])/i', '', $value);

        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString)
            - 1;

        $stringWithCommaOrDot     = preg_replace('/([,\.])/', '', $cleanString,
            $separatorsCountToBeErased);
        $removedThousendSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',
            $stringWithCommaOrDot);

        return (float) str_replace(',', '.', $removedThousendSeparator);
    }
}