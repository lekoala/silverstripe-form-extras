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

    public function getColumnRequired($column)
    {
        return $this->getColumnProperty($column, self::KEY_REQUIRED);
    }

    public function setColumnRequired($column, $req)
    {
        return $this->setColumnProperty($column, self::KEY_REQUIRED, $req);
    }

    public function getColumnsRequired()
    {
        return $this->getProperty(self::KEY_REQUIRED);
    }

    public function setColumnsRequired($required)
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
    public function ColumnsList()
    {
        $list = new ArrayList();

        foreach ($this->columns as $key => $column) {
            $header     = $this->array_get($column, self::KEY_HEADER);
            $required   = $this->array_get($column, self::KEY_REQUIRED);
            $type       = $this->array_get($column, self::KEY_TYPE,
                self::TYPE_TEXT);
            $values     = $this->array_get($column, self::KEY_OPTIONS);
            $valuesList = null;

            $size = null;

            if ($type == self::TYPE_CURRENCY) {
                $type = 'text';
                $size = 7;
            }

            if ($values && is_array($values)) {
                $valuesList = new ArrayList();
                foreach ($values as $k => $v) {
                    $valuesList->push(new ArrayData(array('Name' => $k, 'Value' => $v)));
                }
            }

            $rowData = array(
                'Key' => $key,
                'Size' => $size,
                'Header' => $header,
                'Values' => $valuesList,
                'Type' => $type,
                'Required' => $required ? 'true' : 'false'
            );
            $list->push(new ArrayData($rowData));
        }
        return $list;
    }

    public function DataList()
    {
        $list = new ArrayList();

        if (!$this->value) {
            return $list;
        }

        $val = $this->value;

        $cols    = array_keys($this->columns);
        $subcols = array_keys($this->subColumns);

        $i = 0;
        foreach ($val as $data) {
            $i++;

            $arr = $data;
            if (is_object($arr)) {
                $arr = get_object_vars($arr);
            }

            $rows                 = new ArrayList();
            $subcolumnsToAdd      = array();
            $subcolumnsHaveValues = false;
            foreach ($arr as $k => $v) {
                if (in_array($k, $subcols)) {
                    if ($v) {
                        $subcolumnsHaveValues = true;
                    }
                    $subcolumnsToAdd[] = array(
                        'Name' => $k,
                        'Label' => $this->subColumns[$k][self::KEY_HEADER],
                        'Value' => $v
                    );
                }

                // Ignore unknown columns
                if (!in_array($k, $cols)) {
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

            foreach ($subcolumnsToAdd as $subcolumnToAdd) {
                $list->push(new ArrayData(array(
                    'ID' => $i,
                    'SubColumn' => 1,
                    'SubcolumnsHaveValues' => $v,
                    'ColSpan' => $rows->count(),
                    'Rows' => new ArrayData($subcolumnToAdd)
                )));
            }
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
    protected $readonly = true;
}
