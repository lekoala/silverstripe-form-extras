<?php

/**
 * A dropdown field using chosen
 *
 * @author Koala
 */
class ChosenField extends ListboxField
{

    protected $no_results_text;
    protected $allow_single_deselect = true;
    protected $allow_max_selected;
    protected $use_order = false;
    protected $disable_search = null;
    protected $disable_search_threshold = 10;

    public function __construct($name, $title = null, $source = array(), $value = '', $form = null, $emptyString = null)
    {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
        $this->no_results_text = _t('ChosenField.NO_RESULTS', 'Oops, nothing found!');
    }

    public function Field($properties = array())
    {
        FormExtraJquery::include_jquery();
        // Use updated version of Chosen
        Requirements::block(FRAMEWORK_ADMIN_DIR . '/thirdparty/chosen/chosen/chosen.css');
        Requirements::block(FRAMEWORK_ADMIN_DIR . '/thirdparty/chosen/chosen/chosen.jquery.js');
        Requirements::css(FORM_EXTRAS_PATH . '/javascript/chosen/chosen.min.css');
        Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/chosen/chosen.jquery.min.js');
        if ($this->use_order) {
            Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/chosen_order/chosen.order.jquery.min.js');
        }

        // Init
        $opts = array(
            'no_results_text' => $this->no_results_text,
            'allow_single_deselect' => $this->allow_single_deselect ? true : false
        );
        if (self::config()->rtl) {
            $this->addExtraClass('chosen-rtl');
        }
        if ($this->allow_max_selected) {
            $opts['allow_max_selected'] = $this->allow_max_selected;
        }
        if ($this->disable_search !== null) {
            $opts['disable_search'] = $this->disable_search;
        }
        if ($this->disable_search_threshold > 0) {
            $opts['disable_search_threshold'] = $this->disable_search_threshold;
        }
        if ($this->use_order) {
            $stringValue = $this->value;
            if (is_array($stringValue)) {
                $stringValue = implode(',', $stringValue);
            }
            $this->setAttribute('data-chosen-order', $stringValue);
        }
        $this->setAttribute('data-chosen', json_encode($opts));
        Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/ChosenField.js');
        return $this->buildField($properties);
    }

    /**
     * Returns a <select> tag containing all the appropriate <option> tags
     */
    public function buildField($properties = array())
    {
        if ($this->multiple) {
            $this->name .= '[]';
        }

        $source = $this->getSource();
        $options = array();

        if ($this->getHasEmptyDefault()) {
            $selected = ($this->value === '' || $this->value === null);
            $disabled = (in_array('', $this->disabledItems, true)) ? 'disabled' : false;

            $options[] = new ArrayData(array(
                'Value' => '',
                'Title' => $this->getEmptyString(),
                'Selected' => $selected,
                'Disabled' => $disabled
            ));
        }

        if ($source) {
            // We have an array of values
            if (is_array($this->value)) {
                // Loop through and figure out which values were selected.
                foreach ($source as $value => $title) {
                    $options[] = new ArrayData(array(
                        'Title' => $title,
                        'Value' => $value,
                        'Selected' => (in_array($value, $this->value) || in_array($value, $this->defaultItems)),
                        'Disabled' => $this->disabled || in_array($value, $this->disabledItems),
                    ));
                }
            } else {
                // Listbox was based a singlular value, so treat it like a dropdown.
                foreach ($source as $value => $title) {
                    $selected = false;
                    if ($value === '' && ($this->value === '' || $this->value === null)) {
                        $selected = true;
                    } else {
                        // check against value, fallback to a type check comparison when !value
                        if ($value) {
                            $selected = ($value == $this->value);
                        } else {
                            // Safety check against casting arrays as strings in PHP>5.4
                            if (!is_array($value) && !is_array($this->value)) {
                                $selected = ($value === $this->value) || (((string) $value) === ((string) $this->value));
                            } else {
                                $selected = ($value === $this->value);
                            }
                        }

                        $this->isSelected = $selected;
                    }

                    $disabled = false;
                    if (in_array($value, $this->disabledItems) && $title != $this->emptyString) {
                        $disabled = 'disabled';
                    }

                    $options[] = new ArrayData(array(
                        'Title' => $title,
                        'Value' => $value,
                        'Selected' => $selected,
                        'Disabled' => $disabled,
                    ));
                }
            }
        }


        $properties = array_merge($properties, array(
            'Options' => new ArrayList($options)
        ));

        return FormField::Field($properties);
    }

    public function getDisableSearch()
    {
        return $this->disable_search;
    }

    public function setDisableSearch($disable_search)
    {
        $this->disable_search = $disable_search;
    }

    public function getDisableSearchThreshold()
    {
        return $this->disable_search_threshold;
    }

    public function setDisableSearchThreshold($disable_search_threshold)
    {
        $this->disable_search_threshold = $disable_search_threshold;
        return $this;
    }

    public function getUseOrder()
    {
        return $this->use_order;
    }

    public function setUseOrder($use_order)
    {
        $this->use_order = $use_order;
    }

    public function getNoResultsText()
    {
        return $this->no_results_text;
    }

    public function setNoResultsText($t)
    {
        $this->no_results_text = $t;
    }

    public function getSingleDeselect()
    {
        return $this->allow_single_deselect;
    }

    public function setSingleDeselect($v)
    {
        $this->allow_single_deselect = $v;
    }

    public function getMaxSelected()
    {
        return $this->allow_max_selected;
    }

    public function setMaxSelected($max)
    {
        $this->allow_max_selected = $max;
    }

    public function getDefaultText()
    {
        return $this->getAttribute('data-placeholder');
    }

    public function setDefaultText($text)
    {
        return $this->setAttribute('data-placeholder', $text);
    }
}
