<?php

/**
 * Description of LimitedTextareaField
 *
 * @author LeKoala <thomas@lekoala.be>
 */
class LimitedTextareaField extends TextareaField
{

    public function getLimit()
    {
        $this->getAttribute('data-limit');
    }

    public function setLimit($limit)
    {
        $this->setAttribute('data-limit', $limit);
    }

    function extraClass()
    {
        return 'textarea '.parent::extraClass();
    }

    function Field($properties = array())
    {
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/LimitedTextareaField.js');
        return parent::Field($properties);
    }
}