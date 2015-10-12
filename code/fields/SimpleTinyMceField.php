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
        if ($toolbar) {
            $toolbar = "'".$toolbar."'";
        } else {
            $toolbar = 'false';
        }
        $menubar = $this->getMenubar();
        if ($menubar) {
            $menubar = "'".$menubar."'";
        } else {
            $menubar = 'false';
        }

        $skin = $this->config()->skin;

        $plugins = $this->getPlugins();

        $tools = '';
        if(strpos($plugins, 'table') !== false) {
            $tools ="\n    tools: 'inserttable',";
        }

        // We should update the hidden textarea to make sure validation still works
        Requirements::customScript('var simpleTinymceSetup = function(editor) {
    editor.on("change",function(e) {
        document.getElementById(e.target.id).innerHTML = e.target.contentDocument.innerHTML
    });
}',
            'simpleTinymceSetup');
        
        // Init instance
        Requirements::customScript('tinymce.init({
    selector: "#'.$this->ID().'",
    statusbar : false,
    skin: "'.$skin.'",
    setup: simpleTinymceSetup,
    autoresize_bottom_margin : 0,
    menubar: '.$menubar.',
    toolbar: '.$toolbar.',
    plugins: ["'.$plugins.'"],' . $tools . '
 });');
        return parent::Field($properties);
    }
}