<?php

/**
 * YesNoOptionsetField
 *
 * @author Koala
 */
class YesNoOptionsetField extends OptionsetField
{

    function __construct($name, $title = null, $source = array(), $value = '',
                         $form = null, $emptyString = null)
    {
        if (empty($source)) {
            $source = array(
                '0' => _t('YesNoOptionsetField.YES', 'Yes'),
                '1' => _t('YesNoOptionsetField.NO', 'No'),
            );
        }
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
    }

    function extraClass()
    {
        return 'optionset '.parent::extraClass();
    }
}