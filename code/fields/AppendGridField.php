<?php

/**
 * AppendGridField
 *
 * @author lekoala
 */
class AppendGridField extends FormField
{
    const TYPE_TEXT            = 'text';
    const TYPE_SELECT          = 'select';
    const TYPE_CHECKBOX        = 'checkbox';
    const TYPE_COLOR           = 'color';
    const TYPE_DATE            = 'date';
    const TYPE_DATETIME        = 'datetime';
    const TYPE_DATETIME_LOCAL  = 'datetime-local';
    const TYPE_EMAIL           = 'email';
    const TYPE_NUMBER          = 'number';
    const TYPE_RANGE           = 'range';
    const TYPE_SEARCH          = 'search';
    const TYPE_TEL             = 'tel';
    const TYPE_TIME            = 'time';
    const TYPE_URL             = 'url';
    const TYPE_WEEK            = 'week';
    const TYPE_HIDDEN          = 'hidden';
    const TYPE_UI_DATEPICKER   = 'ui-datepicker';
    const TYPE_UI_SPINNER      = 'ui-spinner';
    const TYPE_UI_AUTOCOMPLETE = 'ui-autcomplete';
    const TYPE_UI_SELECTMENU   = 'ui-selectmenu';
    const TYPE_CUSTOM          = 'custom';

    protected $columns;
    protected $caption;
    protected $captionTooltip;
    protected $initRows       = 1;
    protected $maxRowsAllowed = 0;

    public function Field($properties = array())
    {
        FormExtraJquery::include_jquery();
        FormExtraJquery::include_jquery_ui();
        if (Director::isDev()) {
            Requirements::css(FORM_EXTRAS_PATH.'/javascript/appendgrid/jquery.appendGrid-1.5.2.min.css');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/appendgrid/jquery.appendGrid-1.5.2.min.js');
        } else {
            Requirements::css(FORM_EXTRAS_PATH.'/javascript/appendgrid/jquery.appendGrid-1.5.2.css');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/appendgrid/jquery.appendGrid-1.5.2.js');
        }

        $opts = array();
        if ($this->caption) {
            $opts['caption'] = $this->caption;
        }
        if ($this->captionTooltip) {
            $opts['captionTooltip'] = $this->captionTooltip;
        }
        if ($this->initRows) {
            $opts['initRows'] = $this->initRows;
        }
        if ($this->maxRowsAllowed) {
            $opts['maxRowsAllowed'] = $this->maxRowsAllowed;
        }
        if ($this->value) {
            $opts['initData'] = $this->value;
        }
        if ($this->columns) {
            $opts['columns'] = array_values($this->columns);
        }

        if (FormExtraJquery::isAdminBackend()) {
            Requirements::customScript('var appendgrid_'.$this->ID().' = '.json_encode($opts));
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/AppendGridField.js');
        } else {
            Requirements::customScript('jQuery("#'.$this->ID().'").appendGrid('.json_encode($opts).');');
        }

        return parent::Field($properties);
    }

    public function dataValue()
    {
        if($this->value) {
            return $this->value;
        }
        // if no value is set, look for data in the request
        $id = $this->ID();
        $arr = array();
        foreach($_REQUEST as $key => $val) {
            if(strpos($key, $id) === false) {
                continue;
            }
            $name = str_replace($id . '_','',$key);
            $parts = explode("_", $name);
            if(count($parts) !== 2) {
                continue;
            }
            $num = $parts[1];
            $col = $parts[0];
            if(!isset($arr[$num])) {
                $arr[$num] = array();
            }
            $arr[$num][$col] = $val;
        }
        $this->value = $arr;
        return $this->value;
    }

    public function saveInto(\DataObjectInterface $record)
    {
        $name = $this->name;
        echo '<pre>';
        var_dump($name);
        var_dump($_REQUEST);
        exit();
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    public function addColumn($name, $display = null, $type = 'text',
                              $value = null, $opts = null)
    {
        if (!$display) {
            $display = $name;
        }

        $baseOpts = array(
            'name' => $name,
            'display' => $display,
            'type' => $type
        );

        if ($value !== null) {
            $baseOpts['value'] = $value;
        }

        if ($opts) {
            $baseOpts = array_merge($baseOpts, $opts);
        }

        $this->columns[$name] = $baseOpts;
    }

    public function getColumn($name)
    {
        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        }
    }

    public function removeColumn($name)
    {
        if (isset($this->columns[$name])) {
            unset($this->columns[$name]);
            return true;
        }
        return false;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function setCaption($caption)
    {
        $this->caption = $caption;
        return $this;
    }

    public function getCaptionTooltip()
    {
        return $this->captionTooltip;
    }

    public function setCaptionTooltip($captionTooltip)
    {
        $this->captionTooltip = $captionTooltip;
        return $this;
    }

    public function getInitRows()
    {
        return $this->initRows;
    }

    public function setInitRows($initRows)
    {
        $this->initRows = $initRows;
        return $this;
    }

    public function getMaxRowsAllowed()
    {
        return $this->maxRowsAllowed;
    }

    public function setMaxRowsAllowed($maxRowsAllowed)
    {
        $this->maxRowsAllowed = $maxRowsAllowed;
        return $this;
    }

    public function getInitData()
    {
        return $this->initData;
    }

    public function setInitData($initData)
    {
        $this->initData = $initData;
        return $this;
    }
}