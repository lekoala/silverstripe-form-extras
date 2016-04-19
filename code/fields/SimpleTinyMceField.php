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
    protected $fileManager = null;

    /**
     * @config
     * @var boolean
     */
    private static $prevent_file_manager = false;

    /**
     * @config
     * @var boolean
     */
    private static $file_manager_enabled_by_default = false;

    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);

        if ($this->getFileManager()) {
            FormExtraJquery::include_jquery();
        }
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/tinymce/tinymce.min.js');
        $lang = i18n::get_lang_from_locale(i18n::get_locale());
        if ($lang != 'en') {
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/tinymce/langs/'.$lang.'.js');
        }
    }

    /**
     * Get the responsive file manager state
     *
     * It can be enabled by default through the config
     *
     * @return boolean
     */
    public function getFileManager()
    {
        if ($this->fileManager === null) {
            if ($this->config()->file_manager_enabled_by_default) {
                return true;
            } else {
                return false;
            }
        }
        return $this->fileManager;
    }

    /**
     * Enable the responsive file manager for this tinymce instance
     *
     * @param boolean $fileManager
     * @return boolean File manager enabled or not?
     */
    public function setFileManager($fileManager = true)
    {
        if ($this->config()->prevent_file_manager) {
            return false;
        }
        $this->fileManager = $fileManager;
        return $fileManager;
    }

    public function getMenubar()
    {
        if ($this->menubar === null) {
            return self::config()->menubar;
        }
        return $this->menubar;
    }

    public function setMenubar($menubar)
    {
        $this->menubar = $menubar;
        return $this;
    }

    public function getToolbar()
    {
        if ($this->toolbar === null) {
            return self::config()->toolbar;
        }
        return $this->toolbar;
    }

    public function setToolbar($toolbar)
    {
        $this->toolbar = $toolbar;
        return $this;
    }

    public function setSimpleToolbar()
    {
        return $this->setToolbar(self::config()->simple_toolbar);
    }

    public function getPlugins()
    {
        if ($this->plugins === null) {
            return self::config()->plugins;
        }
        return $this->plugins;
    }

    public function setPlugins($plugins)
    {
        $this->plugins = $plugins;
        return $this;
    }

    protected function enableFileManager()
    {
        $toolbar = $this->getToolbar();
        if ($toolbar) {
            $plugins = $this->getPlugins();
            if (strpos($plugins, 'responsivefilemanager') === false) {
                $plugins .= ' responsivefilemanager';
            }
            $this->setPlugins($plugins);
            if (strpos($toolbar, 'responsivefilemanager') === false) {
                $toolbar .= ' responsivefilemanager';
            }
            $this->setToolbar($toolbar);
        } else {
            // No toolbar, no manager
            $this->fileManager = false;
        }
    }

    public function Field($properties = array())
    {
        if ($this->fileManager) {
            $this->enableFileManager();
        }
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

        $extraJsInit = '';
        if (strpos($plugins, 'table') !== false) {
            $extraJsInit .= ",\n    tools: 'inserttable'";
        }
        if ($this->fileManager) {
            $extraJsInit .= ",\n    external_filemanager_path: '/form-extras/javascript/tinymce/filemanager/'";
            $extraJsInit .= ",\n    filemanager_title: '"._t('SimpleTinyMceField.FILEMANAGER',
                    "File Manager")."'";
            $extraJsInit .= ",\n    external_plugins: {'filemanager':'/form-extras/javascript/tinymce/filemanager/plugin.min.js'}";
        }

        // We should update the hidden textarea to make sure validation still works
        Requirements::customScript('var simpleTinymceSetup = function(editor) {
    editor.on("change",function(e) {
        document.getElementById(e.target.id).innerHTML = e.target.contentDocument.innerHTML
    });
}', 'simpleTinymceSetup');

        // Init instance
        Requirements::customScript('tinymce.init({
    selector: "#'.$this->ID().'",
    statusbar : false,
    skin: "'.$skin.'",
    image_advtab: true,
    setup: simpleTinymceSetup,
    autoresize_bottom_margin : 0,
    menubar: '.$menubar.',
    toolbar: '.$toolbar.',
    plugins: ["'.$plugins.'"]'.$extraJsInit.'
 });');
        return parent::Field($properties);
    }
}