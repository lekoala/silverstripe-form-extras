<?php

/**
 * A checkbox field that always has a value
 *
 * If you want to apply this to all your checkboxes, simply add in mysite/templates/forms the BetterCheckboxField.ss template
 * and rename it to CheckboxField.ss
 *
 * @author Koala
 */
class BetterCheckboxField extends CheckboxField
{
    public function extraClass()
    {
        return parent::extraClass() . ' checkbox';
    }
}
