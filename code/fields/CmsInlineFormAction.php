<?php

/**
 * CmsInlineFormAction
 *
 * A replacement for deprecated InlineFormAction
 *
 * This is not the most robust implementation, but it does the job
 *
 * Action must be implemented on the controller (ModelAdmin for instance)
 * The data passed in the content of the form
 *
 * @author lekoala
 */
class CmsInlineFormAction extends FormField
{
    protected $url;
    protected $redirectURL;
    protected $openInNewWindow = false;
    protected $dialog;
    protected $ajax = true;
    protected $confirmMessage;
    protected static $redirectParams;

    /**
     * Create a new action button.
     * @param action The method to call when the button is clicked
     * @param title The label on the button
     * @param extraClass A CSS class to apply to the button in addition to 'action'
     */
    public function __construct($action, $title = "", $extraClass = '')
    {
        $this->extraClass = ' ' . $extraClass;
        parent::__construct($action, $title, null, null);
    }

    public function performReadonlyTransformation()
    {
        return $this->castedCopy('CmsInlineFormAction_ReadOnly');
    }

    public function getUrl()
    {
        // Some sensible defaults if no url is specified
        if (!$this->url) {
            $ctrl = Controller::curr();
            $action = $this->name;
            if ($ctrl instanceof ModelAdmin) {
                $modelClass = $ctrl->getRequest()->param('ModelClass');
                $action = $modelClass . '/' . $action;
            }
            $params = array();
            if ($this->redirectURL) {
                $params['RedirectURL'] = $this->redirectURL;
            }
            if (self::$redirectParams) {
                $params = array_merge($params, self::$redirectParams);
            }
            if (!empty($params)) {
                $action .= '?' . http_build_query($params);
            }
            return $ctrl->Link($action);
        }
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getRedirectURL()
    {
        return $this->redirectURL;
    }

    public function setRedirectURL($redirectURL)
    {
        $this->ajax = false;
        $this->redirectURL = $redirectURL;
    }

    public function getOpenInNewWindow()
    {
        return $this->openInNewWindow;
    }

    public function setOpenInNewWindow($openInNewWindow)
    {
        $this->ajax = false;
        $this->openInNewWindow = $openInNewWindow;
    }

    public function getConfirmMessage()
    {
        return $this->confirmMessage;
    }

    public function setConfirmMessage($confirmMessage)
    {
        $this->confirmMessage = $confirmMessage;
    }

    public static function getRedirectParams()
    {
        return self::$redirectParams;
    }

    public static function setRedirectParams($redirectParams)
    {
        self::$redirectParams = $redirectParams;
    }

    public function getAjax()
    {
        return $this->ajax;
    }

    public function setAjax($v = true)
    {
        $this->ajax = $v;
        return $this;
    }

    public function getDialog()
    {
        return $this->dialog;
    }

    public function setDialog($v = true)
    {
        $this->dialog = $v;
        return $this;
    }

    public function Field($properties = array())
    {
        Requirements::javascript(FORM_EXTRAS_PATH . "/javascript/CmsInlineFormAction.js");
        $target = '';
        if (!$this->ajax) {
            $this->addExtraClass('no-ajax');
        }
        if ($this->openInNewWindow) {
            $target = ' target="_blank"';
        }
        $confirmMessage = Convert::raw2htmlatt($this->getConfirmMessage());

        return "<input type=\"submit\" name=\"action_{$this->name}\" value=\"{$this->title}\" id=\"{$this->id()}\""
            . " data-url=\"{$this->getUrl()}\" data-dialog=\"{$this->dialog}\" data-ajax=\"{$this->ajax}\""
            . " data-confirm=\"{$confirmMessage}\""
            . " class=\"cmsinlineaction action{$this->extraClass}\"{$target} />";
    }

    public function Title()
    {
        return false;
    }
}

/**
 * Readonly version of {@link CmsInlineFormAction}.
 * @package forms
 * @subpackage actions
 */
class CmsInlineFormAction_ReadOnly extends FormField
{
    protected $readonly = true;

    public function Field($properties = array())
    {
        return "<input type=\"submit\" name=\"action_{$this->name}\" value=\"{$this->title}\" id=\"{$this->id()}\""
            . " disabled=\"disabled\" class=\"action disabled$this->extraClass\" />";
    }

    public function Title()
    {
        return false;
    }
}
