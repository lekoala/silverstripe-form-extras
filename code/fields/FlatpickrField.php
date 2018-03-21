<?php

/**
 * @link https://chmln.github.io/flatpickr
 */
class FlatpickrField extends TextField
{
    /**
     * Override locale. If empty will default to current locale
     *
     * @var string
     */
    protected $locale = null;

    /**
     * Config array
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $rangeInput;

    /**
     * @var string
     */
    protected $disableDateFunctionName;

    private static $default_config = [
        'altInput' => true,
    ];

    public function __construct($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        parent::__construct($name, $title, $value, $maxLength, $form);

        $this->config = self::config()->default_config;
    }

    public function Type()
    {
        return 'flatpickr';
    }

    public function extraClass()
    {
        return 'text ' . parent::extraClass();
    }

    public function getConfig($key)
    {
        if (isset($this->config)) {
            return $this->config[$key];
        }
    }

    /**
     * @return this
     */
    public function setConfig($key, $value)
    {
        if ($value) {
            $this->config[$key] = $value;
        } else {
            unset($this->config[$key]);
        }
        return $this;
    }

    public function getEnableTime()
    {
        return $this->getConfig('enableTime');
    }

    public function setEnableTime($value)
    {
        return $this->setConfig('enableTime', $value);
    }

    public function getNoCalendar()
    {
        return $this->getConfig('noCalendar');
    }

    public function setNoCalendar($value)
    {
        return $this->setConfig('noCalendar', $value);
    }

    public function getAltInput()
    {
        return $this->getConfig('altInput');
    }

    public function setAltInput($value)
    {
        return $this->setConfig('altInput', $value);
    }

    public function getMinDate()
    {
        return $this->getConfig('minDate');
    }

    public function setMinDate($value)
    {
        return $this->setConfig('minDate', $value);
    }

    public function getMaxDate()
    {
        return $this->getConfig('maxDate');
    }

    public function setMaxDate($value)
    {
        return $this->setConfig('maxDate', $value);
    }

    public function getDefaultDate()
    {
        return $this->getConfig('defaultDate');
    }

    public function setDefaultDate($value)
    {
        return $this->setConfig('defaultDate', $value);
    }

    public function getDisabledDates()
    {
        return $this->getConfig('disable');
    }

    /**
     * Disable:
     * - specific dates (iso format)
     * - range of dates (object with from/to keys)
     *
     * @param array $value
     * @return self
     */
    public function setDisabledDates($value)
    {
        return $this->setConfig('disable', $value);
    }

    /**
     * Get locale to use for this field
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale ? : i18n::get_locale();
    }

    /**
     * Determines the presented/processed format based on locale defaults,
     * instead of explicitly setting {@link setDateFormat()}.
     * Only applicable with {@link setHTML5(false)}.
     *
     * @param string $locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * This is required (and ignored) because DBDate use this to scaffold the field
     *
     * @param boolean $bool
     * @return  self
     */
    public function setHTML5($bool)
    {
        return $this;
    }


    /**
     * Get range input
     *
     * @return  string
     */
    public function getRangeInput()
    {
        return $this->rangeInput;
    }

    /**
     * Set range input
     *
     * Warning : currently start and end values are stored
     * in the same input and require extra processing
     * Use with caution!
     *
     * @param string|FormField $rangeInput Range input
     *
     * @return self
     */
    public function setRangeInput($rangeInput)
    {
        if ($rangeInput instanceof FormField) {
            // Prevent any further init on this field
            $rangeInput->addExtraClass("flatpickr-init");
            $rangeInput = $rangeInput->ID();
        }
        $rangeInput = '#' . trim($rangeInput, '#');
        $this->rangeInput = $rangeInput;

        return $this;
    }

    /**
     * Get the value of disableDateFunctionName
     *
     * @return string
     */
    public function getDisableDateFunctionName()
    {
        return $this->disableDateFunctionName;
    }

    /**
     * Set the value of disableDateFunctionName
     *
     * @param string $disableDateFunctionName
     *
     * @return self
     */
    public function setDisableDateFunctionName($disableDateFunctionName)
    {
        $this->disableDateFunctionName = $disableDateFunctionName;
        return $this;
    }

    public function Field($properties = array())
    {
        // Set lang based on locale
        $lang = substr($this->getLocale(), 0, 2);
        if ($lang != 'en') {
            $this->setConfig('locale', $lang);
        }

        Requirements::css('https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.3.2/flatpickr.min.css');
        Requirements::javascript('https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.3.2/flatpickr.js');

        // Locale
        if ($lang != 'en') {
            Requirements::javascript("https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.3.2/l10n/$lang.js");
        }

        // Range
        $this->setAttribute('data-flatpickr', json_encode($this->config));
        if ($this->rangeInput) {
            $this->setAttribute('data-rangeinput', $this->rangeInput);
            Requirements::javascript("https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.3.2/plugins/rangePlugin.js");
        }

        if($this->disableDateFunctionName) {
            $this->setAttribute('data-disabledfunc', $this->disableDateFunctionName);
        }

        Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/FlatpickrField.js');
        return parent::Field($properties);
    }

}
