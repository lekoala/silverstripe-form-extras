<?php

/**
 * FormExtraMulti
 *
 * Multi step form
 *
 * - Define a class name with a number in it (MyFormStep1)
 * - Call definePrevNextActions instead of defining your actions
 * - Define a name in getStepTitle for a nicer name
 * - In your controller, create the form with classForCurrentStep
 *
 * @author lekoala
 */
class FormExtraMulti extends FormExtra
{

    public function FormAction()
    {
        $action = parent::FormAction();
        $action .= '?step=' . self::classNameNumber();
        return $action;
    }

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
     * Get class name for current step based on this class name
     * @return string
     */
    public static function classForCurrentStep()
    {
        $step = self::getCurrentStep();
        if (!$step) {
            $step = 1;
        }
        if (Controller::has_curr()) {
            $requestStep = Controller::curr()->getRequest()->getVar('step');
            if ($requestStep) {
                $step = $requestStep;
            }
        }
        return str_replace(self::classNameNumber(), $step, get_called_class());
    }

    /**
     * Get all steps as an ArrayList. To be used for your templates.
     * @return ArrayList
     */
    public function AllSteps()
    {
        $num = self::classNameNumber();
        if (!$num) {
            return;
        }
        $n = 1;
        $curr = self::getCurrentStep();
        if (!$curr) {
            $curr = 1;
        }
        if (!Controller::has_curr()) {
            return;
        }
        $c = Controller::curr();
        $class = str_replace($num, $n, get_called_class());
        $steps = new ArrayList();

        $baseAction = parent::FormAction();

        while (class_exists($class)) {
            $isCurrent = $isCompleted = false;
            $cssClass = $n == $curr ? 'current' : 'link';
            if ($n == 1) {
                $isCurrent = true;
                $cssClass .= ' first';
            }
            if ($class::isLastStep()) {
                $cssClass .= ' last';
            }
            if ($n < $curr) {
                $isCompleted = true;
                $cssClass .= ' completed';
            }
            $link = $baseAction . '/gotoStep/?step=' . $n;
            $steps->push(new ArrayData(array(
                'Title' => $class::getStepTitle(),
                'Number' => $n,
                'Link' => $link,
                'Class' => $cssClass,
                'IsCurrent' => $isCurrent,
                'IsCompleted' => $isCompleted,
            )));
            $n++;
            $class = str_replace(self::classNameNumber(), $n, get_called_class());
        }
        return $steps;
    }

    /**
     * Clear current step
     * @return void
     */
    public static function clearCurrentStep()
    {
        return Session::clear(self::classNameWithoutNumber() . '.step');
    }

    /**
     * Get current step (defined in session). 0 if not started yet.
     * @return int
     */
    public static function getCurrentStep()
    {
        return (int) Session::get(self::classNameWithoutNumber() . '.step');
    }

    /**
     * Set max step
     * @param int $value
     * @return string
     */
    public static function setMaxStep($value)
    {
        Session::set(self::classNameWithoutNumber() . '.maxStep', (int) $value);
        return Session::save();
    }

    /**
     * Get max step (defined in session). 0 if not started yet.
     * @return int
     */
    public static function getMaxStep()
    {
        return (int) Session::get(self::classNameWithoutNumber() . '.maxStep');
    }

    /**
     * Set current step
     * @param int $value
     * @return string
     */
    public static function setCurrentStep($value)
    {
        $value = (int) $value;
        if ($value > self::getMaxStep()) {
            self::setMaxStep($value);
        }
        Session::set(self::classNameWithoutNumber() . '.step', $value);
        return Session::save();
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
        $next = self::classNameNumber() + 1;
        if ($next == 1) {
            $next++;
        }
        return self::setCurrentStep($next);
    }

    /**
     * Check if this is the last step
     * @return bool
     */
    public static function isLastStep()
    {
        $n = self::classNameNumber();
        $n1 = $n + 1;
        $class = str_replace($n, $n1, get_called_class());
        return !class_exists($class);
    }

    /**
     * Decrement step
     * @return string
     */
    public static function decrementStep()
    {
        $prev = self::classNameNumber() - 1;
        if ($prev < 1) {
            return;
        }
        return self::setCurrentStep($prev);
    }

    /**
     * Return the step name
     * @return string
     */
    public static function getStepTitle()
    {
        // Feel free to implement something nice in your subclass
        return __CLASS__;
    }

    public function checkAccessAction($action)
    {
        if ($action === 'gotoStep') {
            return true;
        }
        return parent::checkAccessAction($action);
    }

    /**
     * Goto a step
     * @return SS_HTTPResponse
     */
    public function gotoStep()
    {
        $step = $this->Controller()->getRequest()->getVar('step');
        if ($step > 0 && $step <= self::getMaxStep()) {
            self::setCurrentStep($step);
        }
        return $this->Controller()->redirectBack();
    }

    /**
     * A basic save action that saves the current step
     * @param array $data
     * @return SS_HTTPResponse
     */
    public function doSave($data)
    {
        $this->saveDataInSession();
        return $this->Controller()->redirectBack();
    }

    /**
     * A basic previous action that decrements the current step
     * @param array $data
     * @return SS_HTTPResponse
     */
    public function doPrev($data)
    {
        self::decrementStep();
        return $this->Controller()->redirectBack();
    }

    /**
     * A basic next action that increments the current step and save the data to the session
     * @param array $data
     * @return SS_HTTPResponse
     */
    public function doNext($data)
    {
        self::incrementStep();
        $this->saveDataInSession();

        // You will need to clear the current step and redirect to something else on the last step
        return $this->Controller()->redirectBack();
    }

    /**
     * Call this instead of manually creating your actions
     *
     * You can easily rename actions by calling $actions->fieldByName('action_doNext')->setTitle('...')
     *
     * @param bool $doSet
     * @return FieldList
     */
    protected function definePrevNextActions($doSet = false)
    {
        $actions = new FieldList();
        $prevClass = 'FormAction';

        // do not validate if used in conjonction with zenvalidator
        if (class_exists('FormActionNoValidation')) {
            $prevClass = 'FormActionNoValidation';
        }

        $prev = null;
        if (self::classNameNumber() > 1) {
            $prev = new $prevClass('doPrev', _t('FormExtra.doPrev', 'Previous'));

            $prev->addExtraClass('step-prev');
            $prev->setUseButtonTag(true);
        }

        $label = _t('FormExtra.doNext', 'Next');
        $actions->push($next = new FormAction('doNext', $label));
        $next->setUseButtonTag(true);
        $next->addExtraClass('step-next');
        if (!$prev) {
            $next->addExtraClass('step-next-single');
        }
        if (self::isLastStep()) {
            $next->setTitle(_t('FormExtra.doFinish', 'Finish'));
            $next->addExtraClass('step-last');
        }

        if ($prev) {
            $actions->push($prev);
        }

        $this->addExtraClass('form-steps');

        if (!$doSet) {
            $this->setActions($actions);
            $actions->setForm($this);
        }

        return $actions;
    }

    /**
     * Same as definePrevNext + a save button
     *
     * @param bool $doSet
     * @return FieldList
     */
    protected function definePrevNextSaveActions($doSet = false)
    {
        $actions = $this->definePrevNextActions($doSet);

        $cls = 'FormAction';

        // do not validate if used in conjonction with zenvalidator
        if (class_exists('FormActionNoValidation')) {
            $cls = 'FormActionNoValidation';
        }
        $save = new $cls('doSave', _t('FormExtra.doSave', 'Save'));
        $save->addExtraClass('step-save');
        $actions->push($save);

        // remove single class
        $next = $actions->fieldByName('action_doNext');
        if ($next) {
            $next->removeExtraClass('step-next-single');
        }

        if (!$doSet) {
            $this->setActions($actions);
            $actions->setForm($this);
        }

        return $actions;
    }

    public static function getDataFromStep($step)
    {
        return Session::get(
                "FormInfo." . self::classNameWithoutNumber() . ".formData.step" . $step);
    }

    public function saveDataInSession()
    {
        Session::set(
            "FormInfo." . self::classNameWithoutNumber() . ".formData.step" . self::classNameNumber(), $this->getData()
        );
    }

    public function getDataFromSession()
    {
        return Session::get(
                "FormInfo." . self::classNameWithoutNumber() . ".formData.step" . self::classNameNumber());
    }

    public function clearDataFromSession()
    {
        return Session::clear(
                "FormInfo." . self::classNameWithoutNumber() . ".formData.step" . self::classNameNumber());
    }

    /**
     * Clear all infos stored in the session from all steps
     */
    public function clearAllDataFromSession()
    {
        self::clearCurrentStep();
        Session::clear(
            "FormInfo." . self::classNameWithoutNumber());
    }
}
