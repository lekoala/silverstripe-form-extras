<?php

/**
 * FormActionArrow
 *
 * @author lekoala
 */
class FormActionArrow extends FormAction {
	
	const BACK = 'back';
	const UP = 'up';
	const RIGHT = 'right';
	const DOWN = 'down';
	
	const POS_LEFT = 'left';
	const POS_RIGHT = 'right';

	protected $arrow;
	protected $arrowPos = 'left';
	
	protected static $arrow_codes = array(
		'left' => '←',
		'up' => '↑',
		'right' => '→',
		'down' => '↓',
		'back' => '↩',
		'next' => '↪',
		'refresh' => '↺',
		'start' => '⇤',
		'end' => '⇥'
	);
	
	public function getArrow() {
		return $this->arrow;
	}

	public function setArrow($arrow) {
		if(isset(self::$arrow_codes[$arrow])) {
			if($arrow === 'right') {
				$this->arrowPos = 'right';
			}
			$arrow = self::$arrow_codes[$arrow];
		}
		$this->arrow = $arrow;
		return $this;
	}
	
	public function getArrowPos() {
		return $this->arrowPos;
	}

	public function setArrowPos($arrowPos = 'right') {
		$this->arrowPos = $arrowPos;
		return $this;
	}
	
	public function Title() {
		if($this->arrow) {
			if($this->arrowPos === 'left') {
				return $this->arrow . ' ' . $this->title;
			}
			else {
				return $this->title . ' ' . $this->arrow;
			}
		}
		return $this->title;
	}
	
}
