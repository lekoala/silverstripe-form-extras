<?php

require_once 'Zend/Locale/Data.php';

/**
 * Support class for initializing javascript in a global manner
 *
 * @author Koala
 */
class FormExtraLeftAndMainExtension extends LeftAndMainExtension
{

    public function init()
    {
        // Send default settings according to locale
        $locale    = i18n::get_locale();
        $symbols   = Zend_Locale_Data::getList($locale, 'symbols');
        $currency  = Currency::config()->currency_symbol;
        $decimals  = $symbols['decimal'];
        $thousands = ($decimals == ',') ? ' ' : ',';

        // Accouting needs to be initialized globally
        FormExtraJquery::include_accounting();
        Requirements::customScript(<<<EOT
    window.accounting.settings = {
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
EOT
            , 'accountingInit'
        );

        Requirements::javascript('form-extras/javascript/LeftAndMain.js');
    }
}
