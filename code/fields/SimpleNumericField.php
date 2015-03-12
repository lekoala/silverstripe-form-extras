<?php

/**
 * A numeric field that don't care about the current locale
 *
 * @author Koala
 */
class SimpleNumericField extends TextField
{

    public function setValue($value, $data = array())
    {
        // If passing in a non-string number, or a value
        // directly from a dataobject then localise this number
        if ((is_numeric($value) && !is_string($value)) ||
            ($value && $data instanceof DataObject)
        ) {
            $this->value = $value;
        } else {
            // If an invalid number, store it anyway, but validate() will fail
            $this->value = $this->clean($value);
        }
        return $this;
    }

    /**
     * In some cases and locales, validation expects non-breaking spaces
     *
     * @param string $input
     * @return string The input value, with all spaces replaced with non-breaking spaces
     */
    protected function clean($input)
    {
        $nbsp = html_entity_decode('&nbsp;', null, 'UTF-8');
        return str_replace(' ', $nbsp, trim($input));
    }

    /**
     * Determine if the current value is a valid number
     *
     * @return bool
     */
    protected function isNumeric()
    {
        return is_numeric($this->clean($this->value));
    }

    public function Type()
    {
        return 'numeric text';
    }

    public function validate($validator)
    {
        if (!$this->value && !$validator->fieldIsRequired($this->name)) {
            return true;
        }

        if ($this->isNumeric()) {
            return true;
        }
        
        $validator->validationError(
            $this->name,
            _t(
                'NumericField.VALIDATION',
                "'{value}' is not a number, only numbers can be accepted for this field",
                array('value' => $this->value)
            ), "validation"
        );
        return false;
    }

    /**
     * Extracts the number value from the localised string value
     *
     * @return string number value
     */
    public function dataValue()
    {
        if (!$this->isNumeric()) {
            return 0;
        }
        $number = $this->clean($this->value);
        return $number;
    }

    /**
     * Returns a readonly version of this field
     */
    public function performReadonlyTransformation()
    {
        $field = new NumericField_Readonly($this->name, $this->title,
            $this->value);
        $field->setForm($this->form);
        return $field;
    }
}