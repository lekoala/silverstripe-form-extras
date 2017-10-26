<?php

/**
 * FormExtra
 *
 * Extend your forms from this class instead of Form to add extra functionalities
 * - Shortcut for returning messages or errors
 * - Autoload form data on error
 *
 * @author lekoala
 */
class FormExtra extends Form
{

    const MSG_NOTICE = 'notice';
    const MSG_WARNING = 'warning';
    const MSG_BAD = 'bad';
    const MSG_GOOD = 'good';

    protected $keepSessionAlive = true;
    protected $dataLossWarning = false;
    protected $dataLossMessage = null;

    public function __construct($controller = null, $name = null, FieldList $fields = null, FieldList $actions = null, $validator = null)
    {
        if ($controller === null) {
            $controller = Controller::curr();
        }
        if ($name === null) {
            $name = get_called_class();
        }
        if ($fields === null) {
            $fields = new FieldList;
        }
        if ($actions === null) {
            $actions = new FieldList;
        }
        parent::__construct($controller, $name, $fields, $actions, $validator);
        $this->restoreDataFromSession();

        $this->dataLossMessage = _t('FormExtra.DATALOSS_MESSAGE', 'Are you sure you want to leave? Any unsaved work may be lost.');
    }

    public function forTemplate()
    {
        // Keep session alive if there is a logged in member
        if ($this->keepSessionAlive && Member::currentUserID()) {
            Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/ping.js');
        }
        // Make sure the user is aware that data is not saved before leaving
        if ($this->dataLossWarning) {
            Requirements::javascriptTemplate(FORM_EXTRAS_PATH . '/javascript/beforeunload.js', array('message' => $this->dataLossMessage));
        }
        return parent::forTemplate();
    }

    /**
     * Store the back url to allow redirect after submitting the form
     *
     * @return void
     */
    public function storeRedirectURL()
    {
        $ctrl = $this->getController();
        $this->Fields()->push(new HiddenField('BackURL', null, $ctrl->getRequest()->getVar('BackURL')));
    }

    public function getKeepSessionAlive()
    {
        return $this->keepSessionAlive;
    }

    /**
     * Define if we want to have a little js script that pings Security/ping
     *
     * @param bool $keepSessionAlive
     */
    public function setKeepSessionAlive($keepSessionAlive = true)
    {
        $this->keepSessionAlive = $keepSessionAlive;
    }

    public function getDataLossWarning()
    {
        return $this->dataLossWarning;
    }

    public function setDataLossWarning($dataLossWarning)
    {
        $this->dataLossWarning = $dataLossWarning;
        return $this;
    }

    public function getDataLossMessage()
    {
        return $this->dataLossMessage;
    }

    public function setDataLossMessage($dataLossMessage)
    {
        $this->dataLossMessage = $dataLossMessage;
        return $this;
    }

    /**
     * Set a message on the controller. Useful is the form is not displayed
     * in the redirected page
     *
     * @param string $message
     * @param string $type
     */
    public function setControllerMessage($message, $type = 'good')
    {
        $this->Controller()->SetSessionMessage($message, $type);
    }

    public function saveDataInSession()
    {
        Session::set(
            "FormInfo.{$this->FormName()}.formData",
            $this->getData()
        );
    }

    public function clearDataFromSession()
    {
        return Session::clear(
            "FormInfo.{$this->FormName()}.formData"
        );
    }

    public function getDataFromSession()
    {
        return Session::get(
            "FormInfo.{$this->FormName()}.formData"
        );
    }

    public function restoreDataFromSession()
    {
        $data = $this->getDataFromSession();
        if ($data) {
            $this->loadDataFrom($data, Form::MERGE_IGNORE_FALSEISH);
        }
    }

    /**
     * Shortcut for an error
     *
     * @param string $msg
     * @param string $url
     * @return SS_HTTPResponse
     */
    protected function err($msg, $url = null)
    {
        $this->saveDataInSession();
        return $this->msg($msg, self::MSG_BAD, $url);
    }

    /**
     * Shortcut for a success
     *
     * @param string $msg
     * @param string $url
     * @return SS_HTTPResponse
     */
    protected function success($msg, $url = null)
    {
        return $this->msg($msg, self::MSG_GOOD, $url);
    }

    /**
     * Return a response with a message for your form
     *
     * @param string $msg
     * @param string $type good,bad,notice,warning
     * @param string $url
     * @return SS_HTTPResponse
     */
    protected function msg($msg, $type, $url = null)
    {
        $this->sessionMessage($msg, $type);
        if ($url) {
            return $this->Controller()->redirect($url);
        }
        return $this->Controller()->redirectBack();
    }

    /**
     * @return ZenValidator
     */
    public function getValidator()
    {
        return parent::getValidator();
    }
}
