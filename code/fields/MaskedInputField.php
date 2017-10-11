<?php

/**
 * A field that use inputmask js
 *
 * We configure that field only through data attributes
 *
 * @link https://github.com/RobinHerbots/jquery.inputmask
 * @author Koala
 */
class MaskedInputField extends TextField
{

    // Base masks
    const MASK_NUMERIC = '9';
    const MASK_ALPHA = 'a';
    const MASK_ALPHANUMERIC = '*';
    // Base alias
    const ALIAS_URL = 'url';
    const ALIAS_IP = 'ip';
    const ALIAS_EMAIL = 'email';
    const ALIAS_DATE = 'date'; // alias of dd/mm/yyyy
    const ALIAS_DATE_DDMMYYYY = 'dd/mm/yyyy';
    const ALIAS_DATE_MMDDYYYY = 'mm/dd/yyyy';
    const ALIAS_DATE_YYYYMMDD = 'yyyy/mm/dd';
    const ALIAS_DATE_ISO = 'yyyy-mm-dd';
    const ALIAS_DATETIME = 'datetime'; // dd/mm/yyyy hh:mm
    const ALIAS_TIME = 'hh:mm:ss';
    const ALIAS_NUMERIC = 'numeric';
    const ALIAS_CURRENCY = 'currency';
    const ALIAS_DECIMAL = 'decimal';
    const ALIAS_INTEGER = 'integer';
    const ALIAS_PHONE = 'phone';
    const ALIAS_PHONEBE = 'phonebe';
    const ALIAS_REGEX = 'regex';

    public function Type() {
		return strtolower('maskedinput');
    }
    
    public function extraClass()
    {
        return 'text ' . parent::extraClass();
    }

    public function Field($properties = array())
    {
        FormExtraJquery::include_jquery();
        Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/inputmask/min/jquery.inputmask.bundle.min.js');

        // Set default options once
        $defaultOpts = self::config()->get('default_options');
        if (empty($defaultOpts)) {
            $defaultOpts = [];
        }
        // Add a alpha mask that accepts spaces and accents
        $defaultOpts['definitions'] = [
            's' => [
                'validator' => "[A-Za-z\u00E0-\u00FC ]",
            ]
        ];
        if (!empty($defaultOpts)) {
            Requirements::customScript('Inputmask.extendDefaults( ' . json_encode($defaultOpts) . ');', 'MaskedInputFieldDefault');
        }

        // Initialize on all input fields once
        Requirements::customScript('jQuery(document).ready(function(){jQuery("input.maskedinput").inputmask();});', 'MaskedInputFieldInit');

        return parent::Field($properties);
    }

    public function validate($validator)
    {
        // This is only called if NO validator is set on the form

        if (!$this->value && !$validator->fieldIsRequired($this->name)) {
            return true;
        }

        // We only validate predetermined aliases and regex
        if (!$this->getAlias() && !$this->getRegex()) {
            return true;
        }

        //@TODO: make sure validation follow more closely js mask
        switch ($this->getAlias()) {
            case self::ALIAS_CURRENCY:
                $prefix = $this->getPrefix() ? $this->getPrefix() : '$ ';
                return is_numeric(str_replace($prefix, '', $this->value));
            case self::ALIAS_DATE:
            case self::ALIAS_DATE_DDMMYYYY:
                $parts = explode('/', $this->value);
                if (count($parts) !== 3) {
                    return false;
                }
                return checkdate($parts[1], $parts[0], $parts[2]);
            case self::ALIAS_DATE_MMDDYYYY:
                $parts = explode('/', $this->value);
                if (count($parts) !== 3) {
                    return false;
                }
                return checkdate($parts[0], $parts[1], $parts[2]);
            case self::ALIAS_DATE_YYYYMMDD:
                if (count($parts) !== 3) {
                    return false;
                }
                $parts = explode('/', $this->value);
                return checkdate($parts[1], $parts[2], $parts[0]);
            case self::ALIAS_DATE_ISO:
                $parts = explode('-', $this->value);
                if (count($parts) !== 3) {
                    return false;
                }
                return checkdate($parts[1], $parts[2], $parts[0]);
            case self::ALIAS_DATETIME:
                $parts = explode('/', substr($this->value, 0, 10));
                if (count($parts) !== 3) {
                    return false;
                }
                return checkdate($parts[1], $parts[2], $parts[0]);
            case self::ALIAS_DECIMAL:
                return is_numeric($this->value);
            case self::ALIAS_EMAIL:
                return filter_var($this->value, FILTER_VALIDATE_EMAIL);
            case self::ALIAS_INTEGER:
                return is_int($this->value);
            case self::ALIAS_IP:
                return filter_var($this->value, FILTER_VALIDATE_IP);
            case self::ALIAS_NUMERIC:
                return is_numeric($this->value);
            case self::ALIAS_PHONE:
                //@TODO: use phonelib class if available
                return true;
            case self::ALIAS_PHONEBE:
                //@TODO: use phonelib class if available
                return true;
            case self::ALIAS_REGEX:
                return preg_match($this->getRegex(), $this->value);
            case self::ALIAS_URL:
                return filter_var($this->value, FILTER_VALIDATE_URL);
            default:
                throw new Exception($this->getAlias() . ' validation not implemented');
        }

        $validator->validationError(
            $this->name, _t(
                'MaskedInputField.VALIDATION', "'{value}' does not respect the required format", array('value' => $this->value)
            ), "validation"
        );
        return false;
    }

    protected function convertBoolToText($bool)
    {
        return $bool ? 'true' : 'false';
    }

    protected function convertTextToBool($text)
    {
        return $text === 'true' ? true : false;
    }

    protected function getBoolAttribute($name)
    {
        return $this->convertTextToBool($this->getAttribute($name));
    }

    protected function setBoolAttribute($name, $value)
    {
        return $this->setAttribute($name, $this->convertBoolToText($value));
    }

    public function getMask()
    {
        return $this->getAttribute('data-inputmask-mask');
    }

    /**
     * Set a mask
     *
     * Examples:
     * - aa-9999
     * - (99) 9999[9]-9999
     * - aa-9{1,4}
     * - (99.9)|(a)
     *
     * @param string $mask
     * @return MaskedInputField
     */
    public function setMask($mask)
    {
        return $this->setAttribute('data-inputmask-mask', $mask);
    }

    public function getSkipOptionalPartCharacter()
    {
        return $this->getBoolAttribute('data-inputmask-skipOptionalPartCharacter');
    }

    public function setSkipOptionalPartCharacter($v = true)
    {
        return $this->setBoolAttribute('data-inputmask-skipOptionalPartCharacter', $v);
    }

    public function getClearMaskOnLostFocus()
    {
        return $this->getBoolAttribute('data-inputmask-clearMaskOnLostFocus');
    }

    public function setClearMaskOnLostFocus($v = true)
    {
        return $this->setBoolAttribute('data-inputmask-clearMaskOnLostFocus', $v);
    }

    public function getGreedy()
    {
        return $this->getBoolAttribute('data-inputmask-greedy');
    }

    public function setGreedy($v = true)
    {
        return $this->setBoolAttribute('data-inputmask-greedy', $v);
    }

    public function getMaskPlaceholder()
    {
        return $this->getBoolAttribute('data-inputmask-placeholder');
    }

    public function setMaskPlaceholder($v)
    {
        return $this->setBoolAttribute('data-inputmask-placeholder', $v);
    }

    public function getAutoUnmask()
    {
        return $this->getBoolAttribute('data-inputmask-autoUnmask');
    }

    public function setAutoUnmask($v = true)
    {
        return $this->setBoolAttribute('data-inputmask-autoUnmask', $v);
    }

    public function getAlias()
    {
        return $this->getAttribute('data-inputmask-alias');
    }

    /**
     * Set mask to a predefined alias
     *
     * See available ALIAS_XXX constants
     *
     * @param string $v
     * @return MaskedInputField
     */
    public function setAlias($v)
    {
        return $this->setAttribute('data-inputmask-alias', $v);
    }

    public function getRegex()
    {
        return $this->getAttribute('data-inputmask-regex');
    }

    public function setRegex($regex)
    {
        $this->setAlias(self::ALIAS_REGEX);
        return $this->setAttribute('data-inputmask-regex', $regex);
    }

    public function getNumericInput()
    {
        return $this->getBoolAttribute('data-inputmask-numericInput');
    }

    public function setNumericInput($v = true)
    {
        return $this->setBoolAttribute('data-inputmask-numericInput', $v);
    }

    public function getAllowMinus()
    {
        return $this->getBoolAttribute('data-inputmask-allowMinus');
    }

    public function setAllowMinus($v = true)
    {
        return $this->setBoolAttribute('data-inputmask-allowMinus', $v);
    }

    public function getAllowPlus()
    {
        return $this->getBoolAttribute('data-inputmask-allowPlus');
    }

    public function setAllowPlus($v = true)
    {
        return $this->setBoolAttribute('data-inputmask-allowPlus', $v);
    }

    public function getRadixPoint()
    {
        return $this->getAttribute('data-inputmask-radixPoint');
    }

    public function setRadixPoint($v)
    {
        return $this->setAttribute('data-inputmask-radixPoint', $v);
    }

    public function getDigits()
    {
        return $this->getAttribute('data-inputmask-digits');
    }

    public function setDigits($v = 2)
    {
        return $this->setAttribute('data-inputmask-digits', $v);
    }

    public function getPrefix()
    {
        return $this->getAttribute('data-inputmask-prefix');
    }

    public function setPrefix($v)
    {
        return $this->setAttribute('data-inputmask-prefix', $v);
    }

    public function getRightAlign()
    {
        return $this->getBoolAttribute('data-inputmask-rightAlign');
    }

    public function setRightAlign($v = true)
    {
        return $this->setBoolAttribute('data-inputmask-rightAlign', $v);
    }
}
