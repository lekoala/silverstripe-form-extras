<?php

class HasOneEditUpdateFormExtension extends \Extension
{

    public function updateEditForm(\Form $form)
    {
        $record    = $form->getRecord();
        $fields    = $form->Fields()->dataFields();
        $separator = HasOneEditDataObjectExtension::separator;
        foreach ($fields as $name => $field) {
            // Replace shortcuts for separator
            $name = str_replace(array(':', '/'), $separator, $name);
            if (!strpos($name, $separator)) {
                // Also skip $name that starts with a separator
                continue;
            }
            $field->setName($name);
            if (!$record) {
                // No record to set value from
                continue;
            }
            if ($field->Value()) {
                // Skip fields that already have a value
                continue;
            }
            list($hasone, $key) = explode($separator, $name, 2);
            if ($record->has_one($hasone) || $record->belongs_to($hasone)) {
                $rel    = $record->getComponent($hasone);
                // Copied from loadDataFrom()
                $exists = (
                    isset($rel->$key) ||
                    $rel->hasMethod($key) ||
                    ($rel->hasMethod('hasField') && $rel->hasField($key))
                    );

                if ($exists) {
                    $value = $rel->__get($key);
                    $field->setValue($value);
                }
            }
        }
    }

    public function updateItemEditForm(\Form $form)
    {
        $this->updateEditForm($form);
    }
}