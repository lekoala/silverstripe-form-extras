<?php

/**
 * PercentageField
 *
 * @author Kalyptus SPRL <thomas@kalyptus.be>
 */
class PercentageField extends DropdownField
{

    public function extraClass()
    {
        return 'dropdown ' . parent::extraClass();
    }

    public function __construct($name, $title = null, $source = array(), $value = '', $form = null, $emptyString = null)
    {
        if (empty($source)) {
            $source = [];
            foreach (range(0, 100) as $i) {
                $v = number_format($i / 100, 2);
                $source[(string) $v] = "$i %";
            }
        }
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
    }
}
