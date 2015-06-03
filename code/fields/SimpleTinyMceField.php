<?php

/**
 * SimpleTinyMceField
 *
 * @author lekoala
 */
class SimpleTinyMceField extends TextareaField
{
    protected $menubar;
    protected $toolbar;
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

    function getMenubar()
    {
        if ($this->menubar === null) {
            return self::config()->menubar;
        }
        return $this->menubar;
    }

    function setMenubar($menubar)
    {
        $this->menubar = $menubar;
        return $this;
    }

    function getToolbar()
    {
        if ($this->toolbar === null) {
            return self::config()->toolbar;
        }
        return $this->toolbar;
    }

    function setToolbar($toolbar)
    {
        $this->toolbar = $toolbar;
        return $this;
    }

    function getPlugins()
    {
        if ($this->plugins === null) {
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
        $toolbar = $this->getToolbar();
        if($toolbar) {
            $toolbar = "'".$toolbar."'";
        }
        else {
            $toolbar = 'false';
        }
        $menubar = $this->getMenubar();
         if($toolbar) {
            $menubar = "'".$menubar."'";
        }
        else {
            $menubar = 'false';
        }

        Requirements::customScript('tinymce.init({
    selector: "#'.$this->ID().'",
    statusbar : false,
    autoresize_bottom_margin : 0,
	menubar: '.$menubar.',
    toolbar: '.$toolbar.',
    plugins: [
         "'.$this->getPlugins().'"
   ],
 });');
        return parent::Field($properties);
    }
}