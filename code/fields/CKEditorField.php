<?php

/**
 * Replace your textarea by a CKEditor instance
 *
 * @author LeKoala
 */
class CKEditorField extends TextareaField
{

    const CDN_SOURCE = "//cdn.ckeditor.com/{version}/{package}/ckeditor.js";
    const PACKAGE_BASIC = 'basic';
    const PACKAGE_STANDARD = 'standard';
    const PACKAGE_FULL = 'full';
    const PACKAGE_CUSTOM = 'custom';
    const TOOLBAR_FULL = 'full';
    const TOOLBAR_ADVANCED = 'advanced';
    const TOOLBAR_BASIC = 'basic';
    const VERSION = '4.7.1';
    const REMOVE_PLUGINS = 'elementspath';
    const RESIZE_ENABLED = false;
    const UPDATE_AS_YOU_TYPE = true;

    public static $check_temp_uploads = true;
    protected $package;
    protected $version;
    protected $scriptSrc;
    protected $toolbar;
    protected $removePlugins;
    protected $resizeEnabled;
    protected $updateAsYouType;

    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);

        $config = self::config();

        $this->version = $config->version ? $config->version : self::VERSION;
        $this->package = $config->package ? $config->package : self::PACKAGE_CUSTOM;

        if ($this->package == self::PACKAGE_CUSTOM) {
            $this->scriptSrc = FORM_EXTRAS_PATH . '/javascript/ckeditor/ckeditor.js';

            $this->toolbar = $config->toolbar ? $config->toolbar : self::TOOLBAR_ADVANCED;
            $this->removePlugins = ($config->removePlugins !== null) ? $config->removePlugins : self::REMOVE_PLUGINS;
        } else {
            $this->scriptSrc = $this->getCdnUrl();
        }

        $this->resizeEnabled = ($config->resizeEnabled !== null) ? $config->resizeEnabled : self::RESIZE_ENABLED;
        $this->updateAsYouType = ($config->updateAsYouType !== null) ? $config->updateAsYouType : self::UPDATE_AS_YOU_TYPE;
    }

    /**
     * Filter out html content
     *
     * @param string $content
     * @return string
     */
    public static function filterContent($content)
    {
        return strip_tags($content, '<a><span><p><br/><br><ul><ol><li><img><b><strong><i><u><em><video><iframe><blockquote><hr><figure><figcaption><oembed>');
    }

    public function getPackage()
    {
        return $this->package;
    }

    public function setPackage($package)
    {
        $this->package = $package;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getToolbar()
    {
        return $this->toolbar;
    }

    public function setToolbar($toolbar)
    {
        $this->toolbar = $toolbar;
        return $this;
    }

    public function getRemovePlugins()
    {
        return $this->removePlugins;
    }

    public function setRemovePlugins($removePlugins)
    {
        $this->removePlugins = $removePlugins;
        return $this;
    }

    public function getResizeEnabled()
    {
        return $this->resizeEnabled;
    }

    public function setResizeEnabled($resizeEnabled)
    {
        $this->resizeEnabled = $resizeEnabled;
        return $this;
    }

    public function getUpdateAsYouType()
    {
        return $this->updateAsYouType;
    }

    public function setUpdateAsYouType($updateAsYouType)
    {
        $this->updateAsYouType = $updateAsYouType;
        return $this;
    }

    public function getScriptSrc()
    {
        return $this->scriptSrc;
    }

    public function setScriptSrc($scriptSrc)
    {
        $this->scriptSrc = $scriptSrc;
    }

    public function getCdnUrl()
    {
        return str_replace(
            array('{version}', '{package}'), array($this->version, $this->package), self::CDN_SOURCE
        );
    }

    public function Field($properties = array())
    {
        Requirements::javascript($this->getScriptSrc());

        // Init instance
        $id = $this->ID();

        $lang = i18n::get_lang_from_locale(i18n::get_locale());

        $arr = array(
            'language' => $lang,
            'baseHref' => '/',
        );

        if ($this->toolbar) {
            $arr['toolbar'] = $this->toolbar;
        }
        if ($this->removePlugins) {
            $arr['removePlugins'] = $this->removePlugins;
        }
        if ($this->resizeEnabled !== null) {
            $arr['resize_enabled'] = $this->resizeEnabled;
        }

        $opts = json_encode($arr);
        Requirements::customScript("CKEDITOR.replace('$id', $opts)");

        // This may be helpful if you use a javascript validator on the textarea
        if ($this->updateAsYouType) {
            Requirements::customScript("CKEDITOR.instances['$id'].on('change', function() { CKEDITOR.instances['$id'].updateElement() });");
        }

        // Add the security token and other parameters
        $tokenValue = SecurityToken::getSecurityID();
        Requirements::customScript("CKEDITOR.instances['$id'].on('fileUploadRequest', function(evt) {
    var xhr = evt.data.fileLoader.xhr;
    xhr.setRequestHeader('X-Csrf', '$tokenValue' );
});");

        return parent::Field($properties);
    }

    public function saveInto(\DataObjectInterface $record)
    {
        if ($this->name) {
            $dataValue = $this->dataValue();

            $originalValue = null;
            if (self::$check_temp_uploads) {
                $originalValue = $record->{$this->name};

                $dataValue = $this->checkTemporaryUploads($dataValue, $record);

                AjaxUploadController::deleteUnusedFiles($originalValue, $dataValue);
            }

            $record->setCastedField($this->name, $dataValue);
        }
    }

    public function checkTemporaryUploads($content, DataObject $record)
    {
        $tmpFiles = AjaxUploadController::findTemporaryUploads($content);

        if (!empty($tmpFiles)) {
            // Use own folder if method exists
            if ($record->hasMethod('getUploadFolder')) {
                $destFolder = $record->getUploadFolder();
            } else {
                $destFolder = '/' . get_class($record) . '/' . $record->ID;
            }
            $content = AjaxUploadController::moveTemporaryUploads($content, $destFolder, $tmpFiles);
        }

        return $content;
    }
}
