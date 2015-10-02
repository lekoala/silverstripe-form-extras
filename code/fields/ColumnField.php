<?php

/**
 * Description of ColumnField
 *
 * @author LeKoala <thomas@lekoala.be>
 */
class ColumnField extends CompositeField
{
    private static $prefix = 'col-';

    public function __construct($position, $children = null)
    {
        parent::__construct($children);
        $this->addExtraClass(self::$prefix . $position);
    }
}