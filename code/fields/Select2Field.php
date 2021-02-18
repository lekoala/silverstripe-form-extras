<?php

/**
 * A field that use Select2
 *
 * Use V4 by default since it's now compatible with legacy jquery
 *
 * @author Koala
 */
class Select2Field extends ListboxField
{

    const SEPARATOR = ',';

    protected $allow_single_deselect = true;
    protected $allow_max_selected;
    protected $tags;
    protected $token_separators = array(',', ' ');
    protected $ajax;
    protected $free_order;
    protected $min_input;
    protected $template_result;
    protected $template_selection;
    protected $min_results_for_search;
    protected $dropdown_parent;

    public function __construct($name, $title = null, $source = array(), $value = '', $form = null, $emptyString = null)
    {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
    }

    public static function RequirementsForV3()
    {
        Requirements::css(FORM_EXTRAS_PATH . '/javascript/select2-v3/select2.css');
        Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/select2-v3/select2.min.js');
        // Locale support
        $lang = i18n::get_lang_from_locale(i18n::get_locale());
        if ($lang != 'en') {
            Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/select2-v3/select2_locale_' . $lang . '.js');
        }
    }

    public static function RequirementsForV4()
    {
        // Use full release
        Requirements::css(FORM_EXTRAS_PATH . '/javascript/select2-v4/css/select2.min.css');
        FormExtraJquery::include_mousewheel();
        Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/select2-v4/js/select2.full.min.js');

        // Locale support
        $lang = i18n::get_lang_from_locale(i18n::get_locale());
        if ($lang != 'en') {
            Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/select2-v4/js/i18n/' . $lang . '.js');
        }
    }

    public function Field($properties = array())
    {
        FormExtraJquery::include_jquery();

        $use_v3 = self::config()->use_v3;

        if ($use_v3) {
            self::RequirementsForV3();
        } else {
            self::RequirementsForV4();
        }

        // Build options
        $opts = array();
        if ($this->allow_single_deselect) {
            if ($use_v3) {
                $opts['allow_clear'] = $this->allow_single_deselect;
            } else {
                $opts['allowClear'] = $this->allow_single_deselect;
            }
        }
        if ($this->allow_max_selected) {
            if ($use_v3) {
                $opts['maximumSelectionSize'] = $this->allow_max_selected;
            } else {
                $opts['maximumSelectionLength'] = $this->allow_max_selected;
            }
        }
        if ($this->min_input) {
            $opts['minimumInputLength'] = $this->min_input;
        }
        if ($this->tags) {
            if ($use_v3) {
                if (is_array($this->source)) {
                    $source = array_values($this->source);
                } else {
                    $source = array();
                }
                // Tags is an array
                $opts['tags'] = $source;
                $opts['tokenSeparators'] = $this->token_separators;

                // Not compatible with select
                $this->multiple = false;
                $this->template = 'HiddenField';
            } else {
                // Tags are calculated from options
                $opts['tags'] = $this->tags;
                $opts['token_separators'] = $this->token_separators;
            }
        }
        if ($this->free_order && !$use_v3) {
            $opts['free_order'] = $this->free_order;
        }
        if (self::config()->rtl && !$use_v3) {
            $opts['dir'] = 'rtl';
        }
        if ($this->ajax) {
            $opts['ajax'] = $this->ajax;
        }
        if ($this->getDefaultText()) {
            $opts['placeholder'] = $this->getDefaultText();
        }
        if ($this->min_results_for_search) {
            $opts['minimumResultsForSearch'] = $this->min_results_for_search;
        }

        $fcts = array();

        if ($this->template_result) {
            $opts['templateResult'] = $this->template_result;
            $fcts[] = $this->template_result;
        }
        if ($this->template_selection) {
            $opts['templateSelection'] = $this->template_selection;
            $fcts[] = $this->template_selection;
        }
        if ($this->dropdown_parent) {
            $opts['dropdownParent'] = $this->dropdown_parent;
            $fcts[] = $this->dropdown_parent;
        }

        $jsonOpts = json_encode($opts, JSON_FORCE_OBJECT);

        $this->setAttribute('data-config', $jsonOpts);
        if (FormExtraJquery::isAdminBackend()) {
            Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/Select2Field.js');
        } else {
            foreach ($fcts as $fct) {
                $jsonOpts = str_replace('"' . $fct . '"', $fct, $jsonOpts);
            }
            Requirements::customScript('jQuery("#' . $this->ID() . '").select2(' . $jsonOpts . ');');
        }

        if ($use_v3) {
            // If you need to adjust the size, it's better to use the field container instead
            $this->setAttribute('style', 'width:100%');
        }

        return parent::Field($properties);
    }

    public function getSource()
    {
        $source = parent::getSource();

        if ($this->tags) {
            $values = array_values($source);
            $source = array_combine($values, $values);
        }

        return $source;
    }

    public function getAttributes()
    {
        $attrs = array();
        if (self::config()->use_v3 && $this->tags) {
            $attrs['type'] = 'hidden';
            $values = $this->Value();
            if (is_array($values)) {
                // If we have a source, replace keys by values
                if ($this->source && is_array($this->source)) {
                    $newValues = array();
                    foreach ($values as $val) {
                        if (!isset($this->source[$val])) {
                            continue;
                        }
                        $newValues[] = $this->source[$val];
                    }
                    $values = $newValues;
                }
                $values = implode(self::SEPARATOR, $values);
            }
            $attrs['value'] = $values;
        }
        return array_merge(
            parent::getAttributes(),
            $attrs
        );
    }

    public function setValue($val, $obj = null)
    {
        if (!$val && $obj && $obj instanceof DataObject && $obj->hasMethod($this->name)) {
            $funcName = $this->name;

            if ($this->tags) {
                $val = array();
                foreach ($obj->$funcName() as $o) {
                    $val[] = $o->Title;
                }
            } else {
                $val = array_values($obj->$funcName()->getIDList());
            }
        }
        if ($val && !is_array($val) && $this->multiple) {
            $val = explode(self::SEPARATOR, $val);
        }

        if ($val) {
            if (!$this->multiple && is_array($val)) {
                throw new InvalidArgumentException('Array values are not allowed (when multiple=false).');
            }

            if ($this->multiple) {
                $parts = (is_array($val)) ? $val : preg_split("/ *, */", trim($val));
                if (ArrayLib::is_associative($parts)) {
                    // This is due to the possibility of accidentally passing an array of values (as keys) and titles (as values) when only the keys were intended to be saved.
                    throw new InvalidArgumentException('Associative arrays are not allowed as values (when multiple=true), only indexed arrays.');
                }

                $this->value = $parts;
            } else {

                $this->value = $val;
            }
        } else {
            $this->value = $val;
        }

        return $this;
    }

    public function saveInto(\DataObjectInterface $record)
    {
        // If tags are enabled, saving into a relation will not work properly
        if ($this->tags) {
            $fieldname = str_replace('[]', '', $this->name);
            $relation = ($fieldname && $record && $record->hasMethod($fieldname)) ? $record->$fieldname() : null;

            if (
                $fieldname && $record && $relation &&
                ($relation instanceof RelationList || $relation instanceof UnsavedRelationList)
            ) {
                $idList = (is_array($this->value)) ? array_values($this->value) : array();
                if (!$record->ID) {
                    $record->write(); // record needs to have an ID in order to set relationships
                    $relation = ($fieldname && $record && $record->hasMethod($fieldname)) ? $record->$fieldname() : null;
                }

                $newIdList = array();

                // Tags will be a list of comma separated tags by title
                $class = $relation->dataClass();
                $filterField = 'Title';
                $newList = $class::get()->filter($filterField, $idList);
                $newListMap = $newList->map($filterField, 'ID');

                // Tag will either already exist or need to be created
                foreach ($idList as $id) {
                    if (isset($newListMap[$id])) {
                        $newIdList[] = $newListMap[$id];
                    } else {
                        $obj = new $class;
                        $obj->Title = trim(str_replace(self::SEPARATOR, '', $id));
                        $obj->write();
                        $newIdList[] = $obj->ID;
                    }
                }

                $relation->setByIDList($newIdList);
            } elseif ($fieldname && $record) {
                if ($this->value) {
                    if (is_array($this->value)) {
                        $this->value = implode(self::SEPARATOR, $this->value);
                    }
                    $record->$fieldname = $this->value;
                } else {
                    $record->$fieldname = null;
                }
            }
        } else {
            return parent::saveInto($record);
        }
    }

    public function getDropdownParent()
    {
        return $this->dropdown_parent;
    }

    /**
     * Add a dropdown parent to this field
     *
     * Allows fixing z-index issues in modal for example
     *
     * @param string $dropdown_parent Expression or quoted string
     * @return void
     */
    public function setDropdownParent($dropdown_parent)
    {
        $this->dropdown_parent = $dropdown_parent;
    }

    public function getMinResultsForSearch()
    {
        return $this->min_results_for_search;
    }

    public function setMinResultsForSearch($min_results_for_search)
    {
        $this->min_results_for_search = $min_results_for_search;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags($tags)
    {
        $this->setMultiple($tags);
        $this->tags = $tags;
    }

    public function getTokenSeparators()
    {
        return $this->token_separators;
    }

    public function setTokenSeparators($token_separators)
    {
        $this->token_separators = $token_separators;
    }

    public function getAllowClear()
    {
        return $this->allow_single_deselect;
    }

    public function setAllowClear($v)
    {
        if ($v) {
            $this->setDefaultText(_t('Select2Field.DEFAULT_PLACEHOLDER', "Please select a value"));
        }
        $this->allow_single_deselect = $v;
    }

    public function getSingleDeselect()
    {
        return $this->allow_single_deselect;
    }

    public function setSingleDeselect($v)
    {
        $this->allow_single_deselect = $v;
    }

    public function setHasEmptyDefault($bool)
    {
        $this->setSingleDeselect($bool);
        $this->setAllowClear($bool);
        return parent::setHasEmptyDefault($bool);
    }

    public function getFreeOrder()
    {
        return $this->free_order;
    }

    public function setFreeOrder($free_order)
    {
        $this->free_order = (bool) $free_order;
    }

    public function getMaxSelected()
    {
        return $this->allow_max_selected;
    }

    public function setMaxSelected($max)
    {
        $this->allow_max_selected = $max;
    }

    public function getDefaultText()
    {
        return $this->getAttribute('data-placeholder');
    }

    /**
     * Alias to make api compatible with ChosenField
     *
     * @param type $text
     * @return type
     */
    public function setDefaultText($text)
    {
        return $this->setAttribute('data-placeholder', $text);
    }

    public function getMinInput()
    {
        return $this->min_input;
    }

    public function setMinInput($min_input)
    {
        $this->min_input = $min_input;
    }

    public function getTemplateResult()
    {
        return $this->template_result;
    }

    /**
     * @param string $template_result Name of the JS function to call
     */
    public function setTemplateResult($template_result)
    {
        $this->template_result = $template_result;
    }

    public function getTemplateSelection()
    {
        return $this->template_selection;
    }

    /**
     * @param string $template_result Name of the JS function to call
     */
    public function setTemplateSelection($template_selection)
    {
        $this->template_selection = $template_selection;
    }

    /**
     * @return array
     */
    public function getAjax()
    {
        return $this->ajax;
    }

    /**
     * @link https://select2.github.io/options.html#ajax
     * @param array $arr
     */
    public function setAjax($arr)
    {
        $this->ajax = $arr;
    }

    /**
     * The controller should return an object with a results key
     *
     * Entries should have an id and a text
     *
     * @link https://select2.github.io/options.html#ajax
     * @param string $url
     * @param string $dataType
     * @return void
     */
    public function setAjaxWizard($url, $dataType = 'json')
    {
        $this->ajax = [
            'url' => $url,
            'dataType' => $dataType
        ];
    }

    public function getPlaceholder()
    {
        return $this->getAttribute('placeholder');
    }

    public function setPlaceholder($value)
    {
        return $this->setAttribute('placeholder', $value);
    }

    /**
     * Validate this field
     *
     * @param Validator $validator
     * @return bool
     */
    public function validate($validator)
    {
        $values = $this->value;
        if (!$values) {
            return true;
        }
        if ($this->ajax || $this->tags) {
            return true;
        }

        $strValue = $values;
        if (is_array($strValue)) {
            $strValue = implode(',', $strValue);
        }
        $errorMessage = _t(
            'Select2Field.SOURCE_VALIDATION',
            "Please select a value within the list provided. %s is not a valid option",
            array('value' => $strValue)
        );
        $source = $this->getSourceAsArray();
        if (is_array($values)) {
            if (!array_intersect_key($source, array_flip($values))) {
                $validator->validationError(
                    $this->name,
                    $errorMessage,
                    "validation"
                );
                return false;
            }
        } else {
            if (!array_key_exists($this->value, $source)) {
                $validator->validationError(
                    $this->name,
                    $errorMessage,
                    "validation"
                );
                return false;
            }
        }
        return true;
    }
}
