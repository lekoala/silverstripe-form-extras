<?php

/**
 * TableField
 *
 * @author lekoala
 */
class TableField extends FormField
{
    const TYPE_TEXT    = 'text';
    const TYPE_ARRAY   = 'array';
    const TYPE_NUMBER  = 'number';
    const KEY_HEADER   = 'header';
    const KEY_VALUES   = 'values';
    const KEY_TYPE     = 'type';
    const KEY_REQUIRED = 'required';

    protected $columns = array();

    /**
     * Get column details
     * @param string $key
     * @return array
     */
    function getColumn($key)
    {
        if (isset($this->columns[$key])) {
            return $this->columns[$key];
        }
    }

    /**
     * Set column details
     * @param string $key
     * @param array $col
     * @return \TableField
     */
    function setColumn($key, $col)
    {
        $this->columns[$key] = $col;
        return $this;
    }

    /**
     * Get a property from all columns
     * @param string $property
     */
    function getProperty($property)
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
     * @return \TableField
     */
    function setProperty($property, $arr)
    {
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
    function getColumnProperty($column, $property, $default = null)
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
     * @return \TableField
     */
    function setColumnProperty($column, $property, $value)
    {
        if (!isset($this->columns[$column])) {
            $this->columns[$column] = array();
        }
        $this->columns[$column][$property] = $value;
        return $this;
    }

    function getHeader($column)
    {
        return $this->getColumnProperty($column, self::KEY_HEADER);
    }

    function setHeader($column, $header)
    {
        return $this->setColumnProperty($column, self::KEY_HEADER, $header);
    }

    function getHeaders()
    {
        return $this->getProperty(self::KEY_HEADER);
    }

    function setHeaders($headers)
    {
        return $this->setProperty(self::KEY_HEADER, $headers);
    }

    function getColumnRequired($column)
    {
        return $this->getColumnProperty($column, self::KEY_REQUIRED);
    }

    function setColumnRequired($column, $req)
    {
        return $this->setColumnProperty($column, self::KEY_REQUIRED, $req);
    }

    function getColumnsRequired()
    {
        return $this->getProperty(self::KEY_REQUIRED);
    }

    function setColumnsRequired($required)
    {
        $req = array();
        foreach ($required as $k => $v) {
            if (is_int($k)) {
                $req[$v] = 1;
            } else {
                $req[$k] = 1;
            }
        }
        return $this->setProperty(self::KEY_REQUIRED, $req);
    }

    function getColumnType($column)
    {
        return $this->getColumnProperty($column, self::KEY_TYPE, self::TYPE_TEXT);
    }

    function setColumnType($column, $type)
    {
        return $this->setColumnProperty($column, self::KEY_TYPE, $type);
    }

    function getColumnsTypes()
    {
        return $this->getProperty(self::KEY_TYPE);
    }

    function setColumnsTypes($types)
    {
        return $this->setProperty(self::KEY_TYPE, $types);
    }

    function getColumnValues($column)
    {
        return $this->getColumnProperty($column, self::KEY_VALUES);
    }

    function setColumnValues($column, array $values)
    {
        return $this->setColumnProperty($column, self::KEY_VALUES, $values);
    }

    function getColumnsValues()
    {
        return $this->getProperty(self::KEY_VALUES);
    }

    function setColumnsValues($values)
    {
        return $this->setProperty(self::KEY_VALUES, $values);
    }

    protected function array_get($arr, $key, $default = null)
    {
        if (isset($arr[$key])) {
            return $arr[$key];
        }
        return $default;
    }

    /**
     * Return an array list of columns
     * 
     * @return \ArrayList
     */
    function ColumnsList()
    {
        $list = new ArrayList();


        foreach ($this->columns as $key => $column) {

            $header     = $this->array_get($column, self::KEY_HEADER);
            $required   = $this->array_get($column, self::KEY_REQUIRED);
            $type       = $this->array_get($column, self::KEY_TYPE,
                self::TYPE_TEXT);
            $values     = $this->array_get($column, self::KEY_VALUES);
            $valuesList = null;
            if ($values) {
                $valuesList = new ArrayList();
                foreach ($values as $k => $v) {
                    $valuesList->push(new ArrayData(array('Name' => $k, 'Value' => $v)));
                }
            }

            $list->push(new ArrayData(array(
                'Key' => $key,
                'Header' => $header,
                'Values' => $valuesList,
                'Type' => $type,
                'Required' => $required ? 'true' : 'false'
            )));
        }
        return $list;
    }

    public function Field($properties = array())
    {
        FormExtraJquery::include_jquery();
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/TableField.js');
        return parent::Field($properties);
    }
}