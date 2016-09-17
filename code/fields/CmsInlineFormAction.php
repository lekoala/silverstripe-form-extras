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
    protected static $redirectParams;

    /**
     * Create a new action button.
     * @param action The method to call when the button is clicked
     * @param title The label on the button
     * @param extraClass A CSS class to apply to the button in addition to 'action'
     */
    public function __construct($action, $title = "", $extraClass = '')
    {
        $this->extraClass = ' '.$extraClass;
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
            $ctrl   = Controller::curr();
            $action = $this->name;
            if ($ctrl instanceof ModelAdmin) {
                $modelClass = $ctrl->getRequest()->param('ModelClass');
                $action     = $modelClass.'/'.$action;
            }
            $params = array();
            if($this->redirectURL) {
                $params['RedirectURL'] = $this->redirectURL;
            }
            if(self::$redirectParams) {
                $params = array_merge($params,self::$redirectParams);
            }
            if(!empty($params)) {
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
        $this->redirectURL = $redirectURL;
    }

    public static function getRedirectParams()
    {
        return self::$redirectParams;
    }

    public static function setRedirectParams($redirectParams)
    {
        self::$redirectParams = $redirectParams;
    }

    public function Field($properties = array())
    {
        $script = "var t=jQuery(this);t.attr('disabled','disabled');jQuery.post(t.data('url'),t.parents('form').serialize(),function(r){t.removeAttr('disabled');jQuery.noticeAdd({text:r})})";

        if ($this->redirectURL) {
            $this->addExtraClass('no-ajax');
            $script = "var t=jQuery(this);window.open(t.data('url'));";
        }
        return "<input type=\"submit\" name=\"action_{$this->name}\" value=\"{$this->title}\" id=\"{$this->id()}\""
            ." data-url=\"{$this->getUrl()}\""
            ." class=\"action{$this->extraClass}\" onclick=\"$script\" />";
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
            ." disabled=\"disabled\" class=\"action disabled$this->extraClass\" />";
    }

    public function Title()
    {
        return false;
    }
}