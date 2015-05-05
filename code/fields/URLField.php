<?php
/**
 * Validates for correct URLs, based on RFC 2396.
 * Important if the underlying logic relies on it,
 * e.g. when passing repository URLs to an SVN binary.
 * 
 * @todo Doesn't work on internationalized (non-ASCII) domain names.
 * 
 * @see http://www.faqs.org/rfcs/rfc2396
 */
class URLField extends TextField {
	
	function __construct($name, $title = null, $value = '', $maxLength = null, $form = null) {
		parent::__construct($name, $title, $value, $maxLength, $form);
		$this->addExtraClass('text');
	}

	function validate($validator){
		if(!empty($this->value) && !filter_var($this->value, FILTER_VALIDATE_URL)) {
 			$validator->validationError(
 				$this->name,
				_t('URLField.VALIDATION', "Please enter a valid URL (e.g http://mywebsite.com)."),
				"validation"
			);
			return false;
		} else{
			return true;
		}
	}

}