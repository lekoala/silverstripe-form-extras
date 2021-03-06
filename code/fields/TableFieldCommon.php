<?php

/**
 * Base class for TableField and AppendGridField
 *
 * The following features should be implemented:
 * - Base types (see constants)
 * - Columns
 * - Sub columns
 * - Total row
 * - Base settings (see properties and getter/setter)
 *
 * @author Koala
 */
class TableFieldCommon extends FormField
{
    // Types - use same keys as appendgridfield - only a subset is supported
    const TYPE_TEXT     = 'text';
    const TYPE_SELECT   = 'select';
    const TYPE_NUMBER   = 'number';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_CURRENCY = 'currency'; // Custom type specific to this implementation
    const TYPE_TEXTAREA = 'textarea'; // Custom type specific to this implementation
    // Keys - used for columns definition
    const KEY_NAME      = 'name';
    const KEY_HEADER    = 'display';
    const KEY_VALUE     = 'value';
    const KEY_TYPE      = 'type';
    const KEY_OPTIONS   = 'ctrlOptions';

    protected $columns           = array();
    protected $subColumns        = array();
    protected $caption;
    protected $captionTooltip;
    protected $initRows          = 1; // Base number of rows
    protected $maxRowsAllowed    = 0; // Maximum number of rows
    protected $initData          = null; // Base data for empty fields
    protected $totalRow          = array(); // Make a sum of all values in one column
    protected $requireAccounting = false;

    public function extraClass()
    {
        return parent::extraClass().' input-wrapper';
    }

    public function saveInto(\DataObjectInterface $record)
    {
        $fieldname = $this->name;

        $relation = ($fieldname && $record && $record->hasMethod($fieldname)) ? $record->$fieldname()
                : null;

        $value = $this->dataValue();

        if ($relation) {
            // TODO: Save to relation
        } else {
            if (is_array($value)) {
                $this->value = json_encode(array_values($value));
            }
        }
        parent::saveInto($record);
    }

    public function getValueJson()
    {
        $v = $this->value;
        if (is_array($v)) {
            $v = json_encode($v);
        }
        if (strpos($v, '[') !== 0) {
            return '[]';
        }
        return $v;
    }

    /**
     * Autoset columns for simple structures based on current data
     */
    public function autosetColumns()
    {
        $value = $this->value;
        if (empty($this->columns)) {
            $dataValue = $value;
            if (count($dataValue)) {
                $firstValue = (array) $dataValue[0];
                foreach (array_keys($firstValue) as $header) {
                    $this->addColumn($header);
                }
            }
        }
    }

    public function setValue($value)
    {
        // Allow set raw json as value
        if ($value && is_string($value) && strpos($value, '[') === 0) {
            $value = json_decode($value);
        }
        return parent::setValue($value);
    }

    public function addColumn($name, $display = null, $type = 'text',
                              $value = null, $opts = null)
    {
        if (strpos($name, '_') !== false) {
            throw new Exception('Please avoid _ in a column name');
        }

        if ($display === null) {
            $display = $name;
        }

        if ($type == self::TYPE_CURRENCY) {
            $this->requireAccounting = true;
        }

        // Set a sensible default value for numbers
        if ($type == self::TYPE_NUMBER && $value === null) {
            $value = 0;
        }

        if ($type == self::TYPE_TEXTAREA) {
            throw new Exception('Only use textarea in sub columns');
        }

        // Check for options for select
        if ($type == self::TYPE_SELECT) {
            if ($opts && !isset($opts['ctrlOptions'])) {
                throw new Exception('Please define a "ctrlOptions" in options');
            }
        }

        $baseOpts = array(
            self::KEY_NAME => $name,
            self::KEY_HEADER => $display,
            self::KEY_TYPE => $type
        );

        if ($value !== null) {
            $baseOpts[self::KEY_VALUE] = $value;
        }

        if (!empty($opts)) {
            $baseOpts = array_merge($baseOpts, $opts);
        }

        $this->columns[$name] = $baseOpts;
    }

    public function TotalRow()
    {
        if (empty($this->totalRow)) {
            return false;
        }
        return new ArrayList($this->totalRow);
    }

    /**
     * Add a total row at the end of the table.
     *
     * @param string $field Field to compute (must be of type currency)
     * @param string $name Name of the html input
     * @param string $label Label of the total
     */
    public function addTotalRow($field, $name = null, $label = null)
    {
        if ($name === null) {
            $name = $field;
        }
        if ($label === null) {
            $label = $name;
            $name  = $field;
        }
        $this->totalRow[$field] = array('Field' => $field, 'Name' => $name, 'Label' => $label);
    }

    /**
     * Remove the total row (or a field of it)
     *
     * @param string $field
     */
    public function removeTotalRow($field = null)
    {
        if ($field) {
            if (isset($this->totalRow[$field])) {
                unset($this->totalRow[$field]);
            }
        } else {
            $this->totalRow = array();
        }
    }

    public function getSubColumns()
    {
        return $this->subColumns;
    }

    public function setSubColumns($columns)
    {
        $this->subColumns = $columns;
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
    public function addSubColumn($name, $display = null, $type = 'text',
                                 $value = null, $opts = array())
    {
        if (!$display) {
            $display = $name;
        }

        // Set a sensible default value for numbers
        if ($type == self::TYPE_NUMBER && $value === null) {
            $value = 0;
        }

        $baseOpts = array(
            self::KEY_NAME => $name,
            self::KEY_HEADER => $display,
            self::KEY_TYPE => $type
        );

        if ($value !== null) {
            $baseOpts[self::KEY_VALUE] = $value;
        }

        if (!empty($opts)) {
            $baseOpts = array_merge($baseOpts, $opts);
        }

        $this->subColumns[$name] = $baseOpts;
    }

    public function getSubColumn($name)
    {
        if (isset($this->subColumns[$name])) {
            return $this->subColumns[$name];
        }
    }

    public function removeSubColumn($name)
    {
        if (isset($this->subColumns[$name])) {
            unset($this->subColumns[$name]);
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

    /**
     * Get base number of rows
     *
     * @return int
     */
    public function getInitRows()
    {
        return $this->initRows;
    }

    /**
     * Set base number of rows
     *
     * @param int $initRows
     * @return \TableFieldCommon
     */
    public function setInitRows($initRows)
    {
        $this->initRows = (int) $initRows;
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

    public function performReadonlyTransformation()
    {
        if ($this->isReadonly()) {
            return $this;
        }
        $copy = $this->castedCopy('TableField_ReadOnly');
        $copy->setColumns($this->getColumns());
        $copy->setReadonly(true);
        return $copy;
    }

    /**
     * Get column details
     * @param string $key
     * @return array
     */
    public function getColumn($key)
    {
        if (isset($this->columns[$key])) {
            return $this->columns[$key];
        }
    }

    /**
     * Set column details
     * @param string $key
     * @param array $col
     * @return \TableFieldCommon
     */
    public function setColumn($key, $col)
    {
        $this->columns[$key] = $col;
        return $this;
    }

    /**
     * Remove a column
     * @param string $key
     */
    public function removeColumn($key)
    {
        unset($this->columns[$key]);
    }

    /**
     * Get all columns
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Set all columns
     * @param array $columns
     * @return \TableFieldCommon
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Get a property from all columns
     * @param string $property
     */
    public function getProperty($property)
    {
        $values = array();
        foreach ($this->columns as $key => $col) {
            if (isset($col[$property])) {
                $values[$key] = $col[$property];
            } else {
                $values[$key] = null;
            }
        }
    }

    /**
     * Set a property on all columns
     * @param string $property
     * @param array $arr
     * @return \TableFieldCommon
     */
    public function setProperty($property, $arr)
    {
        if (!ArrayLib::is_associative($arr)) {
            $arr = array_combine($arr, $arr);
        }
        // Make sure all columns exists
        foreach ($arr as $colName => $value) {
            if (!isset($this->columns[$colName])) {
                $this->columns[$colName] = array();
            }
        }
        // Assign values to columns
        foreach ($this->columns as $colName => $colData) {
            if (isset($arr[$colName])) {
                $colData[$property]      = $arr[$colName];
                $this->columns[$colName] = $colData;
            }
        }
        return $this;
    }

    /**
     * Get property from a column
     * @param string $column
     * @param string $property
     * @param mixed $default
     * @return mixed
     */
    public function getColumnProperty($column, $property, $default = null)
    {
        if (!isset($this->columns[$column])) {
            return $default;
        }
        if (!isset($this->columns[$column][$property])) {
            return $default;
        }
        return $this->columns[$column][$property];
    }

    /**
     * Set property of a column
     * @param string $column
     * @param string $property
     * @param mixed $value
     * @return \TableFieldCommon
     */
    public function setColumnProperty($column, $property, $value)
    {
        if (!isset($this->columns[$column])) {
            $this->columns[$column] = array();
        }
        $this->columns[$column][$property] = $value;
        return $this;
    }

    public function getHeader($column)
    {
        return $this->getColumnProperty($column, self::KEY_HEADER);
    }

    public function setHeader($column, $header)
    {
        return $this->setColumnProperty($column, self::KEY_HEADER, $header);
    }

    public function getHeaders()
    {
        return $this->getProperty(self::KEY_HEADER);
    }

    public function setHeaders($headers)
    {
        return $this->setProperty(self::KEY_HEADER, $headers);
    }

    public function getColumnType($column)
    {
        return $this->getColumnProperty($column, self::KEY_TYPE, self::TYPE_TEXT);
    }

    public function setColumnType($column, $type)
    {
        return $this->setColumnProperty($column, self::KEY_TYPE, $type);
    }

    public function getColumnsTypes()
    {
        return $this->getProperty(self::KEY_TYPE);
    }

    public function setColumnsTypes($types)
    {
        return $this->setProperty(self::KEY_TYPE, $types);
    }

    protected function array_get($arr, $key, $default = null)
    {
        if (isset($arr[$key])) {
            return $arr[$key];
        }
        return $default;
    }
}