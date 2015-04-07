<?php

/**
 * MiniColorsField
 *
 * @author lekoala
 */
class MiniColorsField extends TextField
{

    public function __construct($name, $title = null, $value = '',
                                $maxLength = 7, $form = null)
    {
        parent::__construct($name, $title, $value, $maxLength, $form);
        $this->setTheme('silverstripe');
    }

    public function getTheme()
    {
        return $this->getAttribute('data-theme');
    }

    public function setTheme($theme)
    {
        $this->setAttribute('data-theme', $theme);
        return $this;
    }

    public function Type()
    {
        return 'minicolorsfield text';
    }

    public function Field($properties = array())
    {
        FormExtraJquery::include_jquery();
        Requirements::javascript(THIRDPARTY_DIR.'/jquery-entwine/dist/jquery.entwine-dist.js');
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/minicolors/jquery.minicolors.min.js');
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/MiniColorsField.js');
        Requirements::css(FORM_EXTRAS_PATH.'/javascript/minicolors/jquery.minicolors.css');
        return parent::Field($properties);
    }
}