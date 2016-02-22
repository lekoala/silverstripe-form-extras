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

    public function hex6($color)
    {
        $color = str_replace('#', '', $color);
        if (strlen($color) == 6) {
            return '#'.$color;
        }
        if (strlen($color) == 3) {
            return '#'.$color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
        }
        throw new Exception("Invalid color");
    }

    /**
     * Get inverted color
     * 
     * @param string $color
     * @return string
     */
    public function getInvertedColor($color)
    {
        $color = str_replace('#', '', $this->hex6($color));

        if (strlen($color) != 6) {
            return '#000000';
        }

        $rgb = '';
        for ($x = 0; $x < 3; $x++) {
            $c = 255 - hexdec(substr($color, (2 * $x), 2));
            $c = ($c < 0) ? 0 : dechex($c);
            $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
        }
        return '#'.$rgb;
    }

    /**
     * Get a light or dark color based on the contrast required for a good readability
     * 
     * @param string $color
     * @param string $dark
     * @param string $light
     * @return string
     */
    public function getLightOrDark($color, $dark = '#000000', $light = '#FFFFFF')
    {
        $color = str_replace('#', '', $this->hex6($color));

        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));

        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return ($brightness >= 155) ? $dark : $light;
    }

    /**
     * Get rgb value as array
     * 
     * @return array
     */
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

    /**
     * Get rgb value
     *
     * @return string
     */
    public function toRgb()
    {
        return implode(",", $this->toRgbArray());
    }

    /**
     * Get rgba value
     * 
     * @param float $opacity
     * @return string
     */
    public function toRgba($opacity = 0.5)
    {
        return implode(",", $this->toRgbArray()).','.$opacity;
    }
}