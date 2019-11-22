<?php

/**
 * BirthDateField
 *
 * @author lekoala
 */
class BirthDateField extends DateField
{
    protected $fields;
    protected $cache = array();

    public function __construct($name, $title = null, $value = null)
    {
        $this->setFieldHolderTemplate('forms/BirthDateField');

        parent::__construct($name, $title, $value);
    }

    public function setValue($val)
    {
        $this->cache = array();
        if (is_array($val)) {
            $val = $val['year'].'-'.str_pad($val['month'], 2, 0, STR_PAD_LEFT).'-'.str_pad($val['day'],
                    2, 0, STR_PAD_LEFT);
            if($val == '-00-00') {
                $val = ''; // leave it empty;
            }
        }
        return parent::setValue($val);
    }

    public function Year()
    {
        if (isset($this->cache['year'])) {
            return $this->cache['year'];
        }
        $value = $this->dataValue();
        if (!$value) {
            return false;
        }
        $this->cache['year'] = date('Y', strtotime($value));
        return $this->cache['year'];
    }

    public function Month()
    {
        if (isset($this->cache['month'])) {
            return $this->cache['month'];
        }
        $value = $this->dataValue();
        if (!$value) {
            return false;
        }
        $this->cache['month'] = date('n', strtotime($value));
        return $this->cache['month'];
    }

    public function Day()
    {
        if (isset($this->cache['day'])) {
            return $this->cache['day'];
        }
        $value = $this->dataValue();
        if (!$value) {
            return false;
        }
        $this->cache['day'] = date('j', strtotime($value));
        return $this->cache['day'];
    }

    public function Days()
    {
        $list = new ArrayList();
        foreach (range(1, 31) as $v) {
            $list->push(new ArrayData(array(
                'Value' => str_pad($v, 2, 0, STR_PAD_LEFT),
                'Title' => str_pad($v, 2, 0, STR_PAD_LEFT),
                'Selected' => $this->Day() == $v
            )));
        }
        return $list;
    }

    public function Months()
    {
        $list = new ArrayList();
        foreach (range(1, 12) as $v) {
            $list->push(new ArrayData(array(
                'Value' => str_pad($v, 2, 0, STR_PAD_LEFT),
                'Title' => str_pad($v, 2, 0, STR_PAD_LEFT),
                'Selected' => $this->Month() == $v
            )));
        }
        return $list;
    }

    public function Years()
    {
        $list = new ArrayList();
        foreach (range(date('Y') - self::config()->year_range, date('Y')) as $v) { // JD-2019.10.24 Make year range configurable
            $list->push(new ArrayData(array(
                'Value' => $v,
                'Title' => $v,
                'Selected' => $this->Year() == $v
            )));
        }
        return $list;
    }

}
