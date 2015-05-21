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
    const TYPE_CURRENCY        = 'currency'; // Custom type specific to this implementation

    protected $columns;
    protected $caption;
    protected $captionTooltip;
    protected $initRows       = 1;
    protected $maxRowsAllowed = 0;
    protected $initData    = null;

    public function extraClass()
    {
        return parent::extraClass().' input-wrapper';
    }

    public function Field($properties = array())
    {
        FormExtraJquery::include_jquery();

        // Check if we are not using legacy
        if (FormExtraJquery::use_legacy_jquery()) {
            throw new Exception('AppendGrid is not compatible with jquery 1.7 and requires at least 1.11+');
        }

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
        if ($this->value || $this->initData) {
            $val = $this->value ? $this->value : $this->initData;
            $opts['initData'] = array_values($val);
        }
        if ($this->columns) {
            $opts['columns'] = array_values($this->columns);
        }

        $opts['i18n'] = array(
            'append' => _t('AppendGridField.append', 'Append Row'),
            'removeLast' => _t('AppendGridField.removeLast', 'Remove Last Row'),
            'insert' => _t('AppendGridField.insert', 'Insert Row Above'),
            'remove' => _t('AppendGridField.remove', 'Remove Current Row'),
            'moveUp' => _t('AppendGridField.moveUp', 'Move Up'),
            'moveDown' => _t('AppendGridField.moveDown', 'Move Down'),
            'rowDrag' => _t('AppendGridField.rowDrag', 'Sort Row'),
            'rowEmpty' => _t('AppendGridField.rowEmpty', 'This Grid Is Empty'),
        );

        $jsonOpts = json_encode($opts);

        // Make sure custom functions are interpreted as functions and not as string
        $jsonOpts = str_replace('"appendGridCurrencyBuilder"',
            'appendGridCurrencyBuilder', $jsonOpts);
        $jsonOpts = str_replace('"appendGridCurrencyGetter"',
            'appendGridCurrencyGetter', $jsonOpts);
        $jsonOpts = str_replace('"appendGridCurrencySetter"',
            'appendGridCurrencySetter', $jsonOpts);

        if (FormExtraJquery::isAdminBackend()) {
            Requirements::customScript('var appendgrid_'.$this->ID().' = '.$jsonOpts);
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/AppendGridField.js');
        } else {
            Requirements::customScript('jQuery("#'.$this->ID().'").appendGrid('.$jsonOpts.');');
        }

        return parent::Field($properties);
    }

    public function dataValue()
    {
        if ($this->value) {
            return $this->value;
        }
        // if no value is set, look for data in the request
        $id  = $this->ID();
        $arr = array();
        foreach ($_REQUEST as $key => $val) {
            if (strpos($key, $id) === false) {
                continue;
            }
            $name  = str_replace($id.'_', '', $key);
            $parts = explode("_", $name);
            if (count($parts) !== 2) {
                continue;
            }
            $num = $parts[1];
            $col = $parts[0];
            if (!isset($arr[$num])) {
                $arr[$num] = array();
            }
            $arr[$num][$col] = $val;
        }
        $this->value = array_values($arr);
        return $this->value;
    }

    public function setValue($value)
    {
        if ($value && is_string($value)) {
            $value = json_decode($value);
        }
        parent::setValue($value);
    }

    public function saveInto(\DataObjectInterface $record)
    {
        $fieldname = $this->name;

        $relation = ($fieldname && $record && $record->hasMethod($fieldname)) ? $record->$fieldname()
                : null;

        if ($relation) {
            // TODO: Save to relation
        } else {
            if (is_array($this->value)) {
                $this->value = json_encode(array_values($this->value));
            }
        }
        parent::saveInto($record);
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

    /**
     * Add a column to append grid
     *
     * @param string $name
     * @param string $display
     * @param string $type
     * @param string $value
     * @param array $opts
     */
    public function addColumn($name, $display = null, $type = 'text',
                              $value = null, $opts = array())
    {
        if (!$display) {
            $display = $name;
        }

        // Set a sensible default value for numbers
        if ($type == self::TYPE_NUMBER && $value === null) {
            $value = 0;
        }

        // Check for options for select
        if ($type == self::TYPE_SELECT) {
            if (!isset($opts['ctrlOptions'])) {
                throw new Exception('Please define a "ctrlOptions" in options');
            }
        }

        // Replace currency
        if ($type == self::TYPE_CURRENCY) {
            $type = self::TYPE_CUSTOM;

            // Create custom functions to handle this type
            Requirements::customScript(<<<JS
var appendGridToFixed = function (value) {
    var res = parseFloat(value.replace(',','.')).toFixed(2);
    if(res === 'NaN') {
        return '0.00';
    }
    return res;
}
var appendGridCurrencyBuilder = function(parent, idPrefix, name, uniqueIndex) {
    var ctrlId = idPrefix + '_' + name + '_' + uniqueIndex;
    var ctrl = document.createElement('input');
    jQuery(ctrl).attr({ id: ctrlId, name: ctrlId, type: 'text' }).blur(function() { 
        jQuery(this).val(appendGridToFixed(jQuery(this).val()));
    }).appendTo(parent);
                
    return ctrl;
}
var appendGridCurrencyGetter =  function (idPrefix, name, uniqueIndex) {
    var ctrlId = idPrefix + '_' + name + '_' + uniqueIndex;
    return jQuery('#' + ctrlId).val();
}
var appendGridCurrencySetter =  function (idPrefix, name, uniqueIndex, value) {
    var ctrlId = idPrefix + '_' + name + '_' + uniqueIndex;
    return jQuery('#' + ctrlId).val(appendGridToFixed(value,2));
}
JS
                , 'appendGridCurrencyHelper');

            $opts['customBuilder'] = 'appendGridCurrencyBuilder';
            $opts['customGetter']  = 'appendGridCurrencyGetter';
            $opts['customSetter']  = 'appendGridCurrencySetter';

            if ($value === null) {
                $value = '0.00';
            }
        }

        $baseOpts = array(
            'name' => $name,
            'display' => $display,
            'type' => $type
        );

        if ($value !== null) {
            $baseOpts['value'] = $value;
        }

        if (!empty($opts)) {
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