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
                '1' => _t('YesNoOptionsetField.YES', 'Yes'),
                '0' => _t('YesNoOptionsetField.NO', 'No'),
            );
        }
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
    }

    public function Field($properties = array())
    {
        $source  = $this->getSource();
        $odd     = 0;
        $options = array();

        if ($source) {
            foreach ($source as $value => $title) {
                $itemID     = $this->ID().'_'.preg_replace('/[^a-zA-Z0-9]/', '',
                        $value);
                $odd        = ($odd + 1) % 2;
                $extraClass = $odd ? 'odd' : 'even';
                $extraClass .= ' val'.preg_replace('/[^a-zA-Z0-9\-\_]/', '_',
                        $value);

                $options[] = new ArrayData(array(
                    'ID' => $itemID,
                    'Class' => $extraClass,
                    'Name' => $this->name,
                    'Value' => $value,
                    'Title' => $title,
                    // make a stricter comparison
                    'isChecked' => (string) $value === (string) $this->value,
                    'isDisabled' => $this->disabled || in_array($value,
                        $this->disabledItems),
                ));
            }
        }

        $properties = array_merge($properties,
            array(
            'Options' => new ArrayList($options)
        ));

        return $this->customise($properties)->renderWith(
                $this->getTemplates()
        );
    }

    function setValue($value)
    {
        // Avoid setting blank as no
        if ($value === '' || $value === null) {
            return;
        }
        parent::setValue($value);
    }

    function extraClass()
    {
        return 'optionset '.parent::extraClass();
    }
}