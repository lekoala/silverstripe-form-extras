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
    protected $token_separators      = array(',', ' ');
    protected $ajax;
    protected $free_order;

    public function __construct($name, $title = null, $source = array(),
                                $value = '', $form = null, $emptyString = null)
    {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
    }

    public function Field($properties = array())
    {
        FormExtraJquery::include_jquery();

        $use_v3 = self::config()->use_v3;

        if ($use_v3) {
            Requirements::css(FORM_EXTRAS_PATH.'/javascript/select2-v3/select2.css');
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/select2-v3/select2.min.js');
            // Locale support
            $lang = i18n::get_lang_from_locale(i18n::get_locale());
            if ($lang != 'en') {
                Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/select2-v3/select2_locale_'.$lang.'.js');
            }
        } else {
            // Locale support
            $lang = i18n::get_lang_from_locale(i18n::get_locale());
            if ($lang != 'en') {
                Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/select2-v4/i18n/'.$lang.'.js');
            }

            // Use full release
            Requirements::css(FORM_EXTRAS_PATH.'/javascript/select2-v4/css/select2.min.css');
            FormExtraJquery::include_mousewheel();
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/select2-v4/js/select2.full.min.js');
        }

        // Build options
        $opts = array();
        if ($this->allow_single_deselect) {
            if ($use_v3) {
                $opts['allowClear'] = $this->allow_single_deselect;
            } else {
                $opts['allow_clear'] = $this->allow_single_deselect;
            }
        }
        if ($this->allow_max_selected) {
            if ($use_v3) {
                $opts['maximumSelectionSize'] = $this->allow_max_selected;
            } else {
                $opts['maximumSelectionLength'] = $this->allow_max_selected;
            }
        }
        if ($this->tags) {
            if ($use_v3) {
                if (is_array($this->source)) {
                    $source = array_values($this->source);
                } else {
                    $source = array();
                }
                // Tags is an array
                $opts['tags']            = $source;
                $opts['tokenSeparators'] = $this->token_separators;

                // Not compatible with select
                $this->multiple = false;
                $this->template = 'HiddenField';
            } else {
                // Tags are calculated from options
                $opts['tags']             = $this->tags;
                $opts['token_separators'] = $this->token_separators;
                $opts['free_order']       = $this->free_order;
            }
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

        if (FormExtraJquery::isAdminBackend()) {
            Requirements::customScript('var select2_'.$this->ID().' = '.json_encode($opts));
            Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/Select2Field.js');
        } else {
            Requirements::customScript('jQuery("#'.$this->ID().'").select2('.json_encode($opts).');');
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
            $values        = $this->Value();
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
            parent::getAttributes(), $attrs
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
        if ($val && !is_array($val)) {
            $val = explode(self::SEPARATOR, $val);
        }
        return parent::setValue($val, $obj);
    }

    function saveInto(\DataObjectInterface $record)
    {
        // If tags are enabled, saving into a relation will not work properly
        if ($this->tags) {
            $fieldname = str_replace('[]', '', $this->name);
            $relation  = ($fieldname && $record && $record->hasMethod($fieldname))
                    ? $record->$fieldname() : null;

            if ($fieldname && $record && $relation &&
                ($relation instanceof RelationList || $relation instanceof UnsavedRelationList)) {
                $idList = (is_array($this->value)) ? array_values($this->value) : array();
                if (!$record->ID) {
                    $record->write(); // record needs to have an ID in order to set relationships
                    $relation = ($fieldname && $record && $record->hasMethod($fieldname))
                            ? $record->$fieldname() : null;
                }

                $newIdList = array();

                // Tags will be a list of comma separated tags by title
                $class       = $relation->dataClass();
                $filterField = 'Title';
                $newList     = $class::get()->filter($filterField, $idList);
                $newListMap  = $newList->map($filterField, 'ID');

                // Tag will either already exist or need to be created
                foreach ($idList as $id) {
                    if (isset($newListMap[$id])) {
                        $newIdList[] = $newListMap[$id];
                    } else {
                        $obj         = new $class;
                        $obj->Title  = trim(str_replace(self::SEPARATOR, '', $id));
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

    function getTags()
    {
        return $this->tags;
    }

    function setTags($tags)
    {
        $this->setMultiple($tags);
        $this->tags = $tags;
    }

    function getTokenSeparators()
    {
        return $this->token_separators;
    }

    function setTokenSeparators($token_separators)
    {
        $this->token_separators = $token_separators;
    }

    public function getAllowClear()
    {
        return $this->allow_single_deselect;
    }

    public function setAllowClear($v)
    {
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

    function getFreeOrder()
    {
        return $this->free_order;
    }

    function setFreeOrder($free_order)
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

    public function getAjax()
    {
        return $this->ajax;
    }

    public function setAjax($arr)
    {
        $this->ajax = $arr;
    }
}