<?php

/**
 * SexyPasswordField
 *
 * @author Koala
 */
class SexyPasswordField extends PasswordField
{
    protected $showPasswordConstraints = null;
    protected $enableShowHide          = null;

    public function __construct($name, $title = null, $value = "")
    {
        FormExtraJquery::include_jquery();
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/SexyPasswordField.js');
        parent::__construct($name, $title, $value);
    }

    public function extraClass()
    {
        return 'sexy-password '.parent::extraClass();
    }

    public function getRules()
    {
        /* @var $validator PasswordValidator */
        $validator = Member::password_validator();

        $rules = array();

        foreach ((array) $validator as $k => $v) {
            $k = str_replace("\x00*\x00", '', $k); //watch for null bytes!

            if (!in_array($k,
                    array('minLength', 'minScore', 'testNames', 'historicalPasswordCount'))) {
                continue;
            }

            $rules[$k] = $v;
        }
        return $rules;
    }

    public function getRulesJson()
    {
        return json_encode($this->getRules());
    }

    public function Restrictions()
    {
        $rules    = $this->getRules();
        $messages = array();

        foreach ($rules as $k => $v) {
            switch ($k) {
                case 'minLength':
                    $messages['minLength'] = _t('SexyPasswordField.minLength',
                        'Must be at least {v} characters long', array('v' => $v));
                    break;
                case 'minScore':
                    // If minScore is equal testNames length, we can skip this
                    if (count($rules['testNames']) != $v) {
                        $messages['minScore'] = _t('SexyPasswordField.minScore',
                            'Must have at least {v} special characters',
                            array('v' => $v));
                    }
                    break;
                case 'testNames':
                    foreach ($v as $el) {
                        switch ($el) {
                            case 'lowercase':
                                $messages['lowercase']   = _t('SexyPasswordField.lowercase',
                                    'Must contain lowercase characters');
                                break;
                            case 'uppercase':
                                $messages['uppercase']   = _t('SexyPasswordField.uppercase',
                                    'Must contain uppercase characters');
                                break;
                            case 'digits':
                                $messages['digits']      = _t('SexyPasswordField.digits',
                                    'Must contain numbers');
                                break;
                            case 'punctuation':
                                $messages['punctuation'] = _t('SexyPasswordField.punctuation',
                                    'Must contain punctuation');
                                break;
                        }
                    }
                    break;
                case 'historicalPasswordCount':
                    if (Member::currentUserID()) {
                        if ($v > 1) {
                            $messages['historicalPasswordCount'] = _t('SexyPasswordField.historicalPasswordCount',
                                'Must be different than your last {v} passwords',
                                array('v' => $v));
                        } else {
                            $messages['historicalPasswordCount'] = _t('SexyPasswordField.historicalPasswordCountOne',
                                'Must be different than your last password');
                        }
                    }
                    break;
            }
        }
        $html = '<ul class="sp-restrictions">';
        foreach ($messages as $key => $message) {
            $html .= '<li class="sp-restriction-'.$key.'">'.$message.'</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function showPasswordConstraints()
    {
        if ($this->showPasswordConstraints !== null) {
            return $this->showPasswordConstraints;
        }
        return self::config()->show_password_constraints;
    }

    public function setShowPasswordConstraints($v)
    {
        $this->showPasswordConstraints = (bool) $v;
    }

    public function enableShowHide()
    {
        if ($this->enableShowHide !== null) {
            return $this->enableShowHide;
        }
        return self::config()->enable_show_hide;
    }

    public function setEnableShowHide($v)
    {
        $this->enableShowHide = (bool) $v;
    }
}
