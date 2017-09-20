<?php

/**
 * DatetimeDropdownField
 *
 * @author Kalyptus SPRL <thomas@kalyptus.be>
 */
class DatetimeDropdownField extends DatetimeField
{
	/**
     * Get a configuration accessor for this class. Short hand for Config::inst()->get($this->class, .....).
     * @return Config_ForClass|null
     */
    static public function config()
    {
        return Config::inst()->forClass('DatetimeField');
    }

    public function __construct($name, $title = null, $value = "")
    {
        $this->config = $this->config()->default_config;

        $this->dateField = DateField::create($name . '[date]', false)
            ->addExtraClass('fieldgroup-field');

        $this->timeField = new TimeDropdownField($name . '[time]', false);
        $this->timeField->addExtraClass('fieldgroup-field');

        $this->timezoneField = new HiddenField($name . '[timezone]');

        FormField::__construct($name, $title, $value);
    }
}
