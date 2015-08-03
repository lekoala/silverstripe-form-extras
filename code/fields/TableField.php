<?php

/**
 * TableField - display a table in the cms
 * Serves as base implementation of AppendGridField
 *
 * @author lekoala
 */
class TableField extends TableFieldCommon
{
    const KEY_REQUIRED = 'required';

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
            $values     = $this->array_get($column, self::KEY_OPTIONS);
            $valuesList = null;
            if ($values && is_array($values)) {
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

    function DataList()
    {
        $list = new ArrayList();

        if (!$this->value) {
            return $list;
        }

        $val = $this->value;
        if (is_string($val) && strpos($val, '{') === 0) {
            $val = json_decode($val);
        }

        $cols = array_keys($this->columns);

        $i = 0;
        foreach ($val as $data) {
            $i++;

            $arr = $data;
            if (is_object($arr)) {
                $arr = get_object_vars($arr);
            }

            $rows = new ArrayList();
            foreach ($arr as $k => $v) {
                // Ignore unknown columns
                if(!in_array($k, $cols)) {
                    continue;
                }

                $rows->push(new ArrayData(array(
                    'Name' => $k,
                    'Value' => $v
                )));
            }

            $list->push(new ArrayData(array(
                'ID' => $i,
                'Rows' => $rows
            )));
        }

        return $list;
    }

    public function Field($properties = array())
    {
        if (!$this->isReadonly()) {
            FormExtraJquery::include_jquery();
            if ($this->requireAccounting) {
                FormExtraJquery::include_accounting();
            }
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/TableField.js');
        }

        return parent::Field($properties);
    }
}

class TableField_ReadOnly extends TableField
{

}