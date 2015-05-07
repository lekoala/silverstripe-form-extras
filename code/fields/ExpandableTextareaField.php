<?php

/**
 * Description of ExpandableTextAreaField
 *
 * @link https://github.com/brandonaaron/jquery-expandable
 * @author Koala
 */
class ExpandableTextareaField extends TextareaField
{

    function extraClass()
    {
        return 'textarea '.parent::extraClass();
    }

    function Field($properties = array())
    {
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/expandable/jquery.expandable.js');
        Requirements::customScript('jQuery("#'.$this->ID().'").expandable();');
        return parent::Field($properties);
    }
}