<?php

/**
 * Description of CKEditorField
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
    const VERSION = '4.7.1';

    protected $package;
    protected $version;
    protected $scriptSrc;

    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);

        $this->version = self::VERSION;
        $this->package = self::PACKAGE_STANDARD;
        $this->scriptSrc = $this->getCdnUrl();
        
        $this->addExtraClass('typography-exclude');
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
    
    protected function makeToolbarLine($name, $groups = array()) {
        return array(
            'name' => $name,
            'groups' => $groups
        );
    }

    public function Field($properties = array())
    {
        Requirements::javascript($this->getCdnUrl());
         
        // Init instance
        $id = $this->ID();
        
        $lang = i18n::get_lang_from_locale(i18n::get_locale());

        $arr = array(
            'language' => $lang,
        );
        
        $toolbar = array();
        $toolbar[]  =$this->makeToolbarLine('links');
        $toolbar[]  =$this->makeToolbarLine('insert');
        $toolbar[]  =$this->makeToolbarLine('basicstyles');
        $toolbar[]  =$this->makeToolbarLine('paragraph');
        
        if($toolbar) {
         //   $arr['toolbarGroups'] = $toolbar;
        }
        
        $opts = json_encode($arr);
        Requirements::customScript("CKEDITOR.replace('$id', $opts);");

        return parent::Field($properties);
    }
}
