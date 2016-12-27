<?php

/**
 * Description of HTMLOptionsetField
 *
 * @author Kalyptus SPRL <thomas@kalyptus.be>
 */
class HTMLOptionsetField extends OptionsetField
{
    protected $template = 'forms/HTMLOptionsetField';

    public function extraClass()
    {
        return 'optionset ' . parent::extraClass();
    }
}