<?php

/**
 * SimpleTinyMceField
 *
 * @author lekoala
 */
class SimpleTinyMceField extends TextareaField
{
    protected $plugins;

    function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);

        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/tinymce/tinymce.min.js');
        $lang = i18n::get_lang_from_locale(i18n::get_locale());
        if ($lang != 'en') {
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/tinymce/langs/'.$lang.'.js');
        }
    }

    function getPlugins()
    {
        if (!$this->plugins) {
            return self::config()->plugins;
        }
        return $this->plugins;
    }

    function setPlugins($plugins)
    {
        $this->plugins = $plugins;
        return $this;
    }

    function Field($properties = array())
    {
        Requirements::customScript('tinymce.init({
    selector: "#'.$this->ID().'",
	 plugins: [
         "'.$this->getPlugins().'"
   ],
 });');
        return parent::Field($properties);
    }
}