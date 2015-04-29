<?php

/**
 * CheckboxSetOtherField
 *
 * @author lekoala
 */
class CheckboxSetOtherField extends CheckboxSetField
{
    protected $other_text;

    public function __construct($name, $title = null, $source = array(),
                                $value = '', $form = null, $emptyString = null)
    {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
        $this->other_text = _t('CheckboxSetOtherField', 'Other (please specify)');
    }

    public function getOther_text()
    {
        return $this->other_text;
    }

    public function setOther_text($other_text)
    {
        $this->other_text = $other_text;
        return $this;
    }

    public function getOptions()
    {

        $odd = 0;

        $source = $this->source;
        $values = $this->value;
        $items  = array();

        if (!$source) {
            $source = array();
        }

        if ($values instanceof SS_List || is_array($values)) {
            $items = $values;
        } else {
            if ($values === null) {
                $items = array();
            } else {
                $items = explode(',', $values);
                $items = str_replace('{comma}', ',', $items);
            }
        }

        if (is_string($source)) {
            $source = explode(',', $values);
            $source = str_replace('{comma}', ',', $source);
        } else if ($source instanceof SS_List) {
            $source = $source->toArray();
        } else if (is_array($source)) {
            // nothing to do
        } else {
            throw new Exception(__CLASS__." only supports array, SS_List and strings as source");
        }

        // Detect a current custom value
        $diff = array_diff($items, $source);
        if(count($diff)) {
            $last = end($diff);
            $this->setAttribute('data-other-value', Convert::raw2htmlatt($last));
        }

        $source['_'] = $this->other_text;

        $options = array();


        foreach ($source as $value => $item) {
            $title = $item;

            $itemID     = $this->ID().'_'.preg_replace('/[^a-zA-Z0-9]/', '',
                    $value);
            $odd        = ($odd + 1) % 2;
            $extraClass = $odd ? 'odd' : 'even';
            $extraClass .= ' val'.preg_replace('/[^a-zA-Z0-9\-\_]/', '_', $value);

            $options[] = new ArrayData(array(
                'ID' => $itemID,
                'Class' => $extraClass,
                'Name' => "{$this->name}[]",
                'Value' => $title,
                'Title' => $title,
                'isChecked' => in_array($title, $items) || in_array($title,
                    $this->defaultItems),
                'isDisabled' => $this->disabled || in_array($title,
                    $this->disabledItems)
            ));
        }

        $options = new ArrayList($options);

        return $options;
    }

    public function Field($properties = array())
    {
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/CheckboxSetOtherField.js');
        $field = parent::Field($properties);
        return $field;
    }

    public function getAttributesHTML($attrs = null)
    {
        $options = $this->getOptions(); //make sure other value is set in attrs
        $attrs = parent::getAttributesHTML($attrs);
        return $attrs;
    }

    public function saveInto(\DataObjectInterface $record)
    {
        $fieldname = $this->name;
        if ($this->value) {
            $this->value = str_replace(',', '{comma}', $this->value);
            if (is_array($this->value)) {
                $record->$fieldname = implode(',', (array) $this->value);
            }
        } else {
            $record->$fieldname = '';
        }
    }
}