<?php

/**
 * DBColor
 *
 * A field designed to store a hexadecimal color
 *
 * @author lekoala
 */
class DBColor extends Varchar
{

    public function __construct($name = null, $size = 7, $options = array())
    {
        $this->size = $size ? $size : 7;
        parent::__construct($name, $size, $options);
    }

    public function toRgbArray()
    {
        $hex = str_replace("#", "", $this->getValue());

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array($r, $g, $b);
        return $rgb;
    }

    public function toRgb()
    {
        return implode(",", $this->toRgbArray());
    }
}
