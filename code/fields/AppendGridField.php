<?php

/**
 * Implements a AppendGrid table
 *
 * Two new types have been added : 'currency' and 'textarea'
 *
 * @author lekoala
 */
class AppendGridField extends TableFieldCommon
{
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

    public function Field($properties = array())
    {
        FormExtraJquery::include_jquery();
        if ($this->requireAccounting) {
            FormExtraJquery::include_accounting();
        }

        FormExtraJquery::include_jquery_ui();
        if (Director::isDev()) {
            Requirements::css(FORM_EXTRAS_PATH.'/javascript/appendgrid/jquery.appendGrid-1.6.0.css');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/appendgrid/jquery.appendGrid-1.6.0.js');
        } else {
            Requirements::css(FORM_EXTRAS_PATH.'/javascript/appendgrid/jquery.appendGrid-1.6.0.min.css');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/appendgrid/jquery.appendGrid-1.6.0.min.js');
        }

        if (!FormExtraJquery::isAdminBackend()) {
            Requirements::customScript('var appendgrid_'.$this->ID().' = '.$this->buildJsonOpts(true));
        } else {
            Requirements::css(FORM_EXTRAS_PATH.'/javascript/appendgrid/silverstripe.css');
        }
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/AppendGridField.js');

        return parent::Field($properties);
    }

    public function buildJsonOpts($escape = false)
    {

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
            $val              = $this->value ? $this->value : $this->initData;
            $opts['initData'] = array_values($val);
        }
        if ($this->columns) {
            $opts['columns'] = array_values($this->columns);
        }
        if ($this->totalRow) {
            foreach ($opts['columns'] as &$col) {
                if ($col['name'] != $this->totalRow['Field']) {
                    continue;
                }
                if (empty($col['totalRow'])) {
                    $col['totalRow'] = array('TotalRowID' => $this->ID().'TotalRow');
                }
            }
        }
        if (!empty($this->subColumns)) {
            $opts['useSubPanel']     = true;
            $opts['subColumns']      = $this->subColumns;
            $opts['subPanelBuilder'] = 'appendGridSubPanelBuilder';
            $opts['subPanelGetter']  = 'appendGridSubPanelGetter';
            $opts['rowDataLoaded']   = 'appendGridRowDataLoaded';
        }
        if($this->isReadonly() || $this->isDisabled()) {
            $opts['hideButtons'] = array(
                'append' => true,
                'removeLast' => true,
                'insert' => true,
                'remove' => true,
                'moveUp' => true,
                'moveDown' => true,
            );
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

        // Please be aware that this will make the json invalid
        if ($escape) {
            // Make sure custom functions are interpreted as functions and not as string
            $fcts = array(
                'appendGridCurrencyBuilder',
                'appendGridCurrencyGetter',
                'appendGridCurrencySetter',
                'appendGridSubPanelBuilder',
                'appendGridSubPanelGetter',
                'appendGridRowDataLoaded',
            );
            // Escape custom columns event handler
            foreach ($this->columns as $col) {
                if (!empty($col['onChange'])) {
                    $fcts[] = $col['onChange'];
                }
                if (!empty($col['onClick'])) {
                    $fcts[] = $col['onClick'];
                }
            }
            foreach ($fcts as $fct) {
                $jsonOpts = str_replace('"'.$fct.'"', $fct, $jsonOpts);
            }
        }

        return $jsonOpts;
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
        // Replace currency
        if ($type == self::TYPE_CURRENCY) {
            $type = self::TYPE_CUSTOM;

            $opts['customBuilder'] = 'appendGridCurrencyBuilder';
            $opts['customGetter']  = 'appendGridCurrencyGetter';
            $opts['customSetter']  = 'appendGridCurrencySetter';

            if ($value === null) {
                $value = '0.00';
            }

            $this->requireAccounting = true;
        }

        parent::addColumn($name, $display, $type, $value, $opts);
    }
}