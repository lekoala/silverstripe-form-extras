<?php

/**
 * Description of ExpandableTextAreaField
 *
 * @link https://github.com/brandonaaron/jquery-expandable
 * @author Koala
 */
class ExpandableTextareaField extends TextareaField
{

    public function extraClass()
    {
        return 'textarea '.parent::extraClass();
    }

    public function Field($properties = array())
    {
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/expandable/jquery.expandable.js');
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/ExpandableTextareaField.js');
        return parent::Field($properties);
    }
}
