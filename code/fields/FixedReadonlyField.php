<?php

/**
 * A field whose value don't change once it's set (useful when loading data from
 * keeps messing with one of your field)
 *
 * @author Koala
 */
class FixedReadonlyField extends ReadonlyField
{
    protected $fixed =false;

    public function setValue($value)
    {
        if($this->fixed) {
            return $this;
        }
        if($value) {
            $this->fixed = true;
        }
        return parent::setValue($value);
    }
}