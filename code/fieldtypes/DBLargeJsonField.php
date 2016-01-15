<?php

/**
 * DBJsonField
 *
 * A field designed to store a large json string
 *
 * @author lekoala
 */
class DBLargeJsonField extends Text
{

    public function getValue()
    {
        return json_decode($this->value);
    }

    public function setValue($value, $record = null)
    {
        if (!is_string($value)) {
            $value = json_encode($value);
        }
        return parent::setValue($value, $record);
    }
}
