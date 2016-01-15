<?php

/**
 * Description of SexyOptionset
 *
 * @author Koala
 */
class SexyOptionsetField extends OptionsetField
{

    public function extraClass()
    {
        return 'optionset '.parent::extraClass();
    }

    public function Field($properties = array())
    {
        Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/SexyOptionsetField.js');
        return parent::Field($properties);
    }
}
