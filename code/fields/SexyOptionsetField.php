<?php

/**
 * Description of SexyOptionset
 *
 * @author Koala
 */
class SexyOptionsetField extends OptionsetField
{

    function extraClass()
    {
        return 'optionset '.parent::extraClass();
    }

    function Field($properties = array())
    {
        Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/SexyOptionsetField.js');
        return parent::Field($properties);
    }
}