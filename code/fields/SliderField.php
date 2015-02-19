<?php

/**
 * PercentField
 *
 * @author lekoala
 */
class SliderField extends TextField {
	protected $units;
	protected $onlySlider = false;
	protected $editableField = false;
	protected $sliderOptions = array();
	
	public function __construct($name, $title = null, $value = null) {
		parent::__construct($name, $title, $value);
		Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/SliderField.js');
	}
	
	public function SliderOptionsJson() {
		return json_encode($this->sliderOptions);
	}
	
	public function SliderOptions() {
		return $this->sliderOptions;
	}
	
	public function setSliderOptions(array $arr) {
		$this->sliderOptions = $arr;
		return $this;
	}
	
	public function setSliderOption($k,$v) {
		$this->sliderOptions[$k] = $v;
		return $this;
	}
	
	public function Units() {
		return $this->units;
	}
	
	public function setUnits($v) {
		$this->units = $v;
		return $this;
	}
	
	public function OnlySlider() {
		return $this->onlySlider;
	}
	
	public function NotOnlySlider() {
		return !$this->OnlySlider();
	}
	
	public function setOnlySlider($v) {
		$this->onlySlider = $v ? true : false;
		return $this;
	}
	
	public function EditableField() {
		return $this->editableField;
	}
	
	public function NotEditableField() {
		return !$this->EditableField();
	}
	
	public function setEditableField($v) {
		$this->editableField = $v ? true : false;
		return $this;
	}
	
		public function getAttributes() {
		$attrs = parent::getAttributes();
		
		return array_merge(
				$attrs, array(
			'type' => $this->EditableField() ? 'text' : 'hidden',
				)
		);
	}
}
