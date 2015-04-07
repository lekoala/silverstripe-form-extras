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

    public function __construct($name, $title = null, $source = array(),
                                $value = '', $form = null, $emptyString = null)
    {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
        $this->no_results_text = _t('ChosenField.NO_RESULTS',
            'Oops, nothing found!');
    }

    public function Field($properties = array())
    {
        FormExtraJquery::include_jquery();
        // Use updated version of Chosen
        Requirements::block(FRAMEWORK_ADMIN_DIR.'/thirdparty/chosen/chosen/chosen.css');
        Requirements::block(FRAMEWORK_ADMIN_DIR.'/thirdparty/chosen/chosen/chosen.jquery.js');
        Requirements::css(FORM_EXTRAS_PATH.'/javascript/chosen/chosen.min.css');
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/chosen/chosen.jquery.min.js');

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
        Requirements::customScript('jQuery("#'.$this->ID().'").chosen('.json_encode($opts).')');

        return parent::Field($properties);
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