<?php

/**
 * A field that uses accounting for formatting
 *
 * @author Koala
 */
class AccountingField extends TextField
{
    protected static $_locale    = null;
    protected static $_decimals  = null;
    protected static $_thousands = null;
    private static $default_precision = 2;

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

    public function Value()
    {
        return self::format(parent::Value(), $this->getRawPrecision());
    }

    /**
     * Avoid recursive calls when called from Value() method
     * @return int
     */
    public function getRawPrecision()
    {
        if (!isset($this->attributes['data-precision'])) {
            return self::$default_precision;
        }
        return $this->attributes['data-precision'];
    }

    public function getPrecision()
    {
        return $this->getAttribute('data-precision');
    }

    public function setPrecision($value)
    {
        return $this->setAttribute('data-precision', $value);
    }

    public function setValue($value)
    {
        $value = self::unformat($value);
        parent::setValue($value);
    }

    public static function initVariables()
    {
        $locale           = i18n::get_locale();
        $symbols          = Zend_Locale_Data::getList($locale, 'symbols');
        self::$_decimals  = $symbols['decimal'];
        self::$_thousands = (self::$_decimals == ',') ? ' ' : ',';
    }

    /**
     * Format a value as a number
     *
     * @param string $value
     * @param int $precision
     * @return string
     */
    public static function format($value, $precision = null, $locale = null)
    {
        if ($precision === null) {
            $precision = self::$default_precision;
        }
        if ($locale) {
            $currentLocale = self::$_locale;
            self::$_locale = $locale;
        }
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = self::format($val, $precision);
            }
            return $value;
        }

        if (self::$_locale !== i18n::get_locale()) {
            self::initVariables();
        }
        if (self::$_decimals === null) {
            self::initVariables();
        }

        $rawValue = self::unformat($value);

        $formattedValue = number_format($rawValue, $precision, self::$_decimals,
            self::$_thousands);

        if ($locale) {
            self::$_locale = $currentLocale;
        }

        return $formattedValue;
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

        $neg = false;
        if (strpos($value, '-') === 0) {
            $neg = true;
        }

        $cleanString       = preg_replace('/([^0-9\.,])/i', '', $value);
        $onlyNumbersString = preg_replace('/([^0-9])/i', '', $value);

        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString)
            - 1;

        $stringWithCommaOrDot     = preg_replace('/([,\.])/', '', $cleanString,
            $separatorsCountToBeErased);
        $removedThousendSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',
            $stringWithCommaOrDot);

        $value = str_replace(',', '.', $removedThousendSeparator);
        if ($neg) {
            $value = -1 * abs($value);
        }
        return $value;
    }
}
