<?php

/**
 * Please note that HasOneButtonField as severe performance issue and can create
 * thousands of sql requests for no apparent reason...
 *
 * In the meantime, it is recommeded to use a custom PickerField, see ::createPicker
 * for a drop in alternative
 *
 * @link https://github.com/burnbright/silverstripe-hasonefield
 */
class HasOneButtonField extends GridField
{
    protected $record;
    protected $parent;

    /**
     * Create a new has one button field
     *
     * @param string $name Name of the relation on the parent
     * @param string $title Title of the button
     * @param DataObject $parent Parent dataobject
     */
    public function __construct($name, $title, $parent)
    {
        $this->record = $parent->{$name}();
        $this->parent = $parent;
        $config       = GridFieldConfig::create()
            ->addComponent(new GridFieldDetailForm())
            ->addComponent($button       = new GridFieldHasOneEditButton())
        ;
        $button->setButtonName($title);
        $list         = new HasOneButtonRelationList($this->record, $name,
            $parent);
        parent::__construct($name, $title, $list, $config);
    }

    public static function createPicker($name, $title, $parent, $fields = null)
    {
        $record = $parent->{$name}();
        $picker = new HasOnePickerField($parent, $name, $title, $record);
        $picker->getConfig()->removeComponentsByType('PickerFieldAddExistingSearchButton');
        $picker->getConfig()->removeComponentsByType('PickerFieldDeleteAction');
        $picker->getConfig()->removeComponentsByType('GridFieldToolbarHeader');
        $picker->enableEdit();
        if($fields) {
            $fields->push($picker);
        }
        return $picker;
    }

    public function getRecord()
    {
        return $this->record;
    }
}

class GridFieldHasOneEditButton extends GridFieldAddNewButton implements GridField_HTMLProvider
{

    public function getHTMLFragments($gridField)
    {
        $record = $gridField->getRecord();
        if (!$record->exists() || !$record->isInDB()) {
            return parent::getHTMLFragments($gridField); //use parent add button
        }
        $singleton = singleton($gridField->getModelClass());
        if (!$singleton->canCreate()) {
            return array();
        }
        if (!$this->buttonName) {
            // provide a default button name, can be changed by calling {@link setButtonName()} on this component
            $objectName       = $singleton->i18n_singular_name();
            $this->buttonName = _t('GridField.Edit', 'Edit {name}',
                array('name' => $objectName));
        }
        $data = new ArrayData(array(
            'NewLink' => Controller::join_links($gridField->Link('item'),
                $record->ID, 'edit'),
            'ButtonName' => $this->buttonName,
        ));

        return array(
            $this->targetFragment => $data->renderWith('GridFieldAddNewbutton')
        );
    }
}

class HasOneButtonRelationList extends DataList
{
    protected $record;
    protected $name;
    protected $parent;

    public function __construct($record, $name, $parent)
    {
        $this->record = $record;
        $this->name   = $name;
        $this->parent = $parent;
        parent::__construct($record->ClassName);
    }

    public function add($item)
    {
        // Set parent > child relationship
        $this->parent->{$this->name."ID"} = $item->ID;
        $this->parent->write();

        // Set child > parent relationship
        $item->{$this->parent->ClassName."ID"} = $this->parent->ID;
        $item->write();
    }
}