<?php

/**
 * Slider field using jquery ui
 *
 * @author lekoala
 */
class SliderField extends TextField
{
    protected $units;
    protected $onlySlider    = false;
    protected $editableField = false;
    protected $sliderOptions = array();

    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);
    }

    public function Field($properties = array())
    {
        FormExtraJquery::include_jquery();
        FormExtraJquery::include_jquery_ui();
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/SliderField.js');
        return parent::Field($properties);
    }

    public function SliderOptionsJson()
    {
        return json_encode($this->sliderOptions);
    }

    public function SliderOptions()
    {
        return $this->sliderOptions;
    }

    public function setSliderOptions(array $arr)
    {
        $this->sliderOptions = $arr;
        return $this;
    }

    public function setSliderOption($k, $v)
    {
        $this->sliderOptions[$k] = $v;
        return $this;
    }

    /**
     * @param string $v true,min,max
     * @return \SliderField
     */
    public function setRange($v)
    {
        $this->setSliderOption('range', $v);
        return $this;
    }

    public function setMin($v)
    {
        $this->setSliderOption('min', (int) $v);
        return $this;
    }

    public function setMax($v)
    {
        $this->setSliderOption('max', (int) $v);
        return $this;
    }

    public function setValues($v1, $v2)
    {
        $this->setSliderOption('values', array($v1, $v2));
        return $this;
    }

    public function setStep($v)
    {
        $this->setSliderOption('step', (int) $v);
        return $this;
    }

    public function setDisabled($disabled)
    {
        $this->setSliderOption('disabled', (bool) $disabled);
        return parent::setReadonly($disabled);
    }

    public function setReadonly($readonly)
    {
        $this->setSliderOption('disabled', (bool) $readonly);
        return parent::setReadonly($readonly);
    }

    public function Units()
    {
        return $this->units;
    }

    public function setUnits($v)
    {
        $this->units = $v;
        return $this;
    }

    public function OnlySlider()
    {
        return $this->onlySlider;
    }

    public function NotOnlySlider()
    {
        return !$this->OnlySlider();
    }

    public function setOnlySlider($v)
    {
        $this->onlySlider = $v ? true : false;
        return $this;
    }

    public function EditableField()
    {
        return $this->editableField;
    }

    public function NotEditableField()
    {
        return !$this->EditableField();
    }

    public function setEditableField($v)
    {
        $this->editableField = $v ? true : false;
        return $this;
    }

    public function getAttributes()
    {
        $attrs = parent::getAttributes();

        return array_merge(
            $attrs,
            array(
            'type' => $this->EditableField() ? 'text' : 'hidden',
            )
        );
    }
}