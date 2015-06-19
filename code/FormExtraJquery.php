<?php

/**
 * FormExtraJquery
 *
 * A simple jquery helper
 *
 * It can also load "helper" plugins like hammer and mousewheel. This provides
 * a consistent api to load these plugins across new field types.
 *
 * It is also equally important to block the min/not min version to avoid including
 * the script twice which is not efficient and can break the scripts.
 *
 * You can also include all that you need beforehand and then disable the loader
 * to avoid extra calls and computation
 *
 * @author lekoala
 */
class FormExtraJquery extends Object
{
    const JQUERY_FRAMEWORK        = 'framework';
    const JQUERY_V1               = 'v1';
    const JQUERY_V2               = 'v2';
    const JQUERY_UI_FRAMEWORK     = 'framwework';
    const JQUERY_UI_V1            = 'v1';
    const JQUERY_UI_THEME_DEFAULT = 'default';
    const JQUERY_UI_THEME_NONE    = 'none';

    protected static $disabled = false;
    protected static $included = array();

    public static function getDisabled()
    {
        return self::$disabled;
    }

    public static function setDisabled($disabled)
    {
        self::$disabled = $disabled;
    }

    /**
     * Helper to detect if we are in admin or development admin
     *
     * @return boolean
     */
    public static function isAdminBackend()
    {
        /* @var $controller Controller */
        $controller = Controller::curr();
        if (
            $controller instanceof LeftAndMain ||
            $controller instanceof DevelopmentAdmin ||
            $controller instanceof DatabaseAdmin ||
            (class_exists('DevBuildController') && $controller instanceof DevBuildController)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function use_legacy_jquery()
    {
        return self::config()->jquery_version === self::JQUERY_FRAMEWORK;
    }

    /**
     * Include jquery based on your settings (framework, v1 or v2)
     */
    public static function include_jquery()
    {
        if (self::$disabled || in_array('jquery', self::$included)) {
            return;
        }
        // Avoid conflicts with jquery version of the cms
        if (self::isAdminBackend()) {
            self::$included[] = 'jquery';
            return;
        }
        switch (self::config()->jquery_version) {
            case self::JQUERY_FRAMEWORK:
                $path    = THIRDPARTY_DIR.'/jquery/jquery';
                $migrate = false;
                break;
            case self::JQUERY_V1:
                $path    = FORM_EXTRAS_PATH.'/javascript/jquery/jquery-1.11.2';
                $migrate = true;
                break;
            case self::JQUERY_V2:
                $path    = FORM_EXTRAS_PATH.'/javascript/jquery/jquery-2.1.3';
                $migrate = false;
                break;
            default:
                $path    = THIRDPARTY_DIR.'/jquery/jquery';
                break;
        }
        if (self::config()->jquery_migrate !== 'auto') {
            $migrate = self::config()->jquery_migrate;
        }
        // If we don't use the default version, block the framework version
        if ($path !== THIRDPARTY_DIR.'/jquery/jquery') {
            Requirements::block(THIRDPARTY_DIR.'/jquery/jquery.js');
            Requirements::block(THIRDPARTY_DIR.'/jquery/jquery.min.js');
        }
        if (Director::isDev()) {
            Requirements::javascript($path.'.js');
            Requirements::block($path.'.min.js');
        } else {
            Requirements::javascript($path.'.min.js');
            Requirements::block($path.'.js');
        }

        if ($migrate) {
            if (Director::isDev()) {
                Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/jquery-migrate/jquery-migrate-1.2.1.js');
                Requirements::block(FORM_EXTRAS_PATH.'/javascript/jquery-migrate/jquery-migrate-1.2.1.min.js');
            } else {
                Requirements::block(FORM_EXTRAS_PATH.'/javascript/jquery-migrate/jquery-migrate-1.2.1.js');
                Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/jquery-migrate/jquery-migrate-1.2.1.min.js');
            }
        }

        self::$included[] = 'jquery';
    }

    /**
     * Include jquery ui and theme
     */
    public static function include_jquery_ui()
    {
        if (self::$disabled || in_array('jquery_ui', self::$included)) {
            return;
        }
        switch (self::config()->jquery_ui_version) {
            case self::JQUERY_UI_FRAMEWORK:
                $path = THIRDPARTY_DIR.'/jquery-ui/jquery-ui';
                break;
            case self::JQUERY_UI_V1:
                $path = FORM_EXTRAS_PATH.'/javascript/jquery-ui/jquery-ui';
                break;
            default:
                $path = THIRDPARTY_DIR.'/jquery-ui/jquery-ui';
                break;
        }
        if (Director::isDev()) {
            Requirements::javascript($path.'.js');
            Requirements::block($path.'.min.js');
        } else {
            Requirements::javascript($path.'.min.js');
            Requirements::block($path.'.js');
        }
        if (self::config()->jquery_ui_theme != self::JQUERY_UI_THEME_DEFAULT) {
            Requirements::block(THIRDPARTY_DIR.'/jquery-ui-themes/smoothness/jquery-ui.css');
            Requirements::block(THIRDPARTY_DIR.'/jquery-ui-themes/smoothness/jquery-ui.min.css');

            $theme = self::config()->jquery_ui_theme;
            // in case the less styles are used, developer should include it himself
            if ($theme != self::JQUERY_UI_THEME_NONE) {
                Requirements::css($theme);
            }
        } else {
            if (Director::isDev()) {
                Requirements::css(THIRDPARTY_DIR.'/jquery-ui-themes/smoothness/jquery-ui.css');
            } else {
                Requirements::css(THIRDPARTY_DIR.'/jquery-ui-themes/smoothness/jquery-ui.min.css');
            }
        }
        self::$included[] = 'jquery_ui';
    }

    /**
     * Include jquery mousewheel (does not call include jquery, you have to do it by yourself)
     */
    public static function include_mousewheel()
    {
        if (self::$disabled || in_array('mousewheel', self::$included)) {
            return;
        }
        if (Director::isDev()) {
            Requirements::block(FORM_EXTRAS_PATH.'/javascript/jquery-mousewheel/jquery.mousewheel.min.js');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/jquery-mousewheel/jquery.mousewheel.js');
        } else {
            Requirements::block(FORM_EXTRAS_PATH.'/javascript/jquery-mousewheel/jquery.mousewheel.js');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/jquery-mousewheel/jquery.mousewheel.min.js');
        }
        self::$included[] = 'mousewheel';
    }

    /**
     * Include hammer
     */
    public static function include_hammer()
    {
        if (self::$disabled || in_array('hammer', self::$included)) {
            return;
        }
        if (Director::isDev()) {
            Requirements::block(FORM_EXTRAS_PATH.'/javascript/hammer/hammer.min.js');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/hammer/hammer.js');
        } else {
            Requirements::block(FORM_EXTRAS_PATH.'/javascript/hammer/hammer.js');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/hammer/hammer.min.js');
        }
        self::$included[] = 'hammer';
    }

    /**
     * Include jquery scrollTo
     */
    public static function include_scrollTo()
    {
        if (self::$disabled || in_array('scrollTo', self::$included)) {
            return;
        }
        if (Director::isDev()) {
            Requirements::block(FORM_EXTRAS_PATH.'/javascript/scrollTo/jquery.scrollTo.min.js');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/scrollTo/jquery.scrollTo.js');
        } else {
            Requirements::block(FORM_EXTRAS_PATH.'/javascript/scrollTo/jquery.scrollTo.js');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/scrollTo/jquery.scrollTo.min.js');
        }
        self::$included[] = 'scrollTo';
    }

    /**
     * Include jquery form
     */
    public static function include_form()
    {
        if (self::$disabled || in_array('form', self::$included)) {
            return;
        }
        if (Director::isDev()) {
            Requirements::block(FORM_EXTRAS_PATH.'/javascript/form/jquery.form.min.js');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/form/jquery.form.js');
        } else {
            Requirements::block(FORM_EXTRAS_PATH.'/javascript/form/jquery.form.js');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/form/jquery.form.min.js');
        }
        self::$included[] = 'form';
    }

    public static function include_accounting()
    {
        if (self::$disabled || in_array('accouting', self::$included)) {
            return;
        }

        // Send default settings according to locale
        $locale  = i18n::get_locale();
        $symbols = Zend_Locale_Data::getList($locale, 'symbols');
        $currency = Currency::config()->currency_symbol;
        $decimals = $symbols['decimal'];
        $thousands = ($decimals == ',') ? '.' : ',';

        Requirements::customScript(<<<JS
accounting.settings = {
	currency: {
        symbol : "$currency",
		format: "%s%v",
		decimal : "$decimals",
		thousand: "$thousands",
		precision : 2
	},
	number: {
		precision : 0,
		thousand: "$thousands",
		decimal : "$decimals"
	}
}
JS
        );

        if (Director::isDev()) {
            Requirements::block(FORM_EXTRAS_PATH.'/javascript/accounting/accounting.min.js');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/accounting/accounting.js');
        } else {
            Requirements::block(FORM_EXTRAS_PATH.'/javascript/accounting/accounting.js');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/accounting/accounting.min.js');
        }
        self::$included[] = 'accouting';
    }
}