<?php

/**
 * EmbedTextAreaField
 *
 * Requires embed/embed to work properly
 *
 * @author Kalyptus SPRL <thomas@kalyptus.be>
 */
class EmbedTextareaField extends TextareaField
{

    public function extraClass()
    {
        return 'textarea ' . parent::extraClass();
    }

    public function Field($properties = array())
    {
        if (!class_exists(Embed\Embed::class)) {
            throw new Exception('Please require embed/embed');
        }
        Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/' . __CLASS__ . '.js');
        return parent::Field($properties);
    }
}
