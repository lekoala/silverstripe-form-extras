<?php

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

        // Call this in whatever script using accouting to make sure it's properly initialized
        Requirements::insertHeadTags(<<<EOT
<script type="text/javascript">
//<![CDATA[
function applyAccountingSettings() {
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
}
applyAccountingSettings();
//]]>
</script>
EOT
            , 'applyAccountingSettings'
        );
    }
}