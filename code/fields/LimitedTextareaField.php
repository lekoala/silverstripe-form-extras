<?php

/**
 * Description of LimitedTextareaField
 *
 * @author LeKoala <thomas@lekoala.be>
 */
class LimitedTextareaField extends TextareaField
{
    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);
        $this->setLimit(150);
    }
    public function getLimit()
    {
        $this->getAttribute('data-limit');
    }

    public function setLimit($limit)
    {
        $this->setAttribute('data-limit', $limit);
    }

    public function extraClass()
    {
        return 'textarea '.parent::extraClass();
    }

    public function Field($properties = array())
    {
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/LimitedTextareaField.js');
        return parent::Field($properties);
    }
}
