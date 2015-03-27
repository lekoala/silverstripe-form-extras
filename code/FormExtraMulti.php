<?php

/**
 * FormExtraMulti
 * 
 * Multi step form
 * 
 * - Define a class name with a number in it (MyFormStep1)
 * - Call definePrevNextActions instead of defining your actions
 * - Define a name in getStepTitle for a nicer name
 * 
 * @author lekoala
 */
class FormExtraMulti extends FormExtra
{

    /**
     * Get class name without any number in it
     * @return string
     */
    public static function classNameWithoutNumber()
    {
        return preg_replace('/[0-9]+/', '', get_called_class());
    }

    /**
     * Get number from class name
     * @return string
     */
    public static function classNameNumber()
    {
        return preg_replace('/[^0-9]+/', '', get_called_class());
    }

    /**
     * Get current step
     * @return int
     */
    public static function getCurrentStep()
    {
        return Session::get(self::classNameWithoutNumber().'.step');
    }

    /**
     * Set current step
     * @param int $value
     * @return string
     */
    public static function setCurrentStep($value)
    {
        return Session::set(self::classNameWithoutNumber().'.step', (int) $value);
    }

    /**
     * Increment step
     * @return string
     */
    public static function incrementStep()
    {
        if (self::isLastStep()) {
            return;
        }
        $next = self::getCurrentStep() + 1;
        return self::setCurrentStep($next);
    }

    /**
     * Check if this is the last step
     * @return bool
     */
    public static function isLastStep()
    {
        $n     = self::classNameNumber();
        $n1    = $n + 1;
        $class = str_replace($n, $n1, get_called_class());
        return !class_exists($class);
    }

    /**
     * Decrement step
     * @return string
     */
    public static function decrementStep()
    {
        $prev = self::getCurrentStep() - 1;
        if ($prev < 1) {
            return;
        }
        return self::setCurrentStep($prev);
    }

    /**
     * Return the step name
     * @return string
     */
    public function getStepTitle()
    {
        // Feel free to implement something nice in your subclass
        return static::classNameWithoutNumber();
    }

    public function doPrev()
    {
        self::decrementStep();
        return $this->Controller()->redirect($this->Controller()->Link());
    }

    public function doNext($data)
    {
        self::incrementStep();
        $this->saveDataInSession();
        return $this->Controller()->redirect($this->Controller()->Link());
    }

    /**
     * Call this instead of manually creating your actions
     * @param bool $doNotSet
     * @return FieldList
     */
    protected function definePrevNextActions($doNotSet = false)
    {
        $actions   = new FieldList();
        $prevClass = 'FormAction';

        // do not validate if used in conjonction with zenvalidator
        if (class_exists('FormActionNoValidation')) {
            $prevClass = 'FormActionNoValidation';
        }

        if (self::getCurrentStep() > 1) {
            $actions->push(new $prevClass('doPrev',
                _t('FormExtra.doPrev', 'Previous')));
        }

        $label = _t('FormExtra.doNext', 'Next');
        if (self::isLastStep()) {
            $label = _t('FormExtra.doFinish', 'Finish');
        }
        $actions->push(new FormAction('doNext', $label));

        if (!$doNotSet) {
            $this->setActions($actions);
            $actions->setForm($this);
        }

        return $actions;
    }

    protected function saveDataInSession()
    {
        Session::set(
            "FormInfo.".self::classNameWithoutNumber().".formData.step".self::classNameNumber(),
            $this->getData()
        );
    }

    protected function getDataFromSession()
    {
        return Session::get(
                "FormInfo.".self::classNameWithoutNumber().".formData.step".self::classNameNumber());
    }
}