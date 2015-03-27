<?php

/**
 * TableField
 *
 * @author lekoala
 */
class TableField extends FormField
{
    protected $headers;
    protected $columnsValues = array();

    function getHeaders()
    {
        return $this->headers;
    }

    function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    function getColumnValues($key)
    {
        if (isset($this->columnsValues[$key])) {
            return $this->columnsValues[$key];
        }
    }

    function setColumnValues($key, $values)
    {
        $this->columnsValues[$key] = $values;
    }

    function HeadersList()
    {
        $list = new ArrayList();
        foreach ($this->headers as $header) {

            $values     = $this->getColumnValues($header);
            $valuesList = null;
            if ($values) {
                $valuesList = new ArrayList();
                foreach ($values as $k => $v) {
                    $valuesList->push(new ArrayData(array('Name' => $k, 'Value' => $v)));
                }
            }

            $list->push(new ArrayData(array(
                'Name' => $header,
                'Values' => $valuesList
            )));
        }
        return $list;
    }

    public function Field($properties = array())
    {
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/TableField.js');
        return parent::Field($properties);
    }
}