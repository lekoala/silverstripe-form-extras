<?php

/**
 * FrontendUploadField
 *
 * @link http://doc.silverstripe.org/framework/en/trunk/reference/uploadfield
 * @author lekoala
 */
class FrontendUploadField extends UploadField
{
    /**
     * @var array
     */
    private static $allowed_actions = array(
        'upload',
        'attach',
        'handleItem',
        'handleSelect',
        'handleGallery',
        'fileexists'
    );
    protected $useCropbox           = true;
    protected $useFocuspoint        = false;
    protected $galleryUrl           = null;

    public function __construct($name, $title = null, \SS_List $items = null)
    {
        parent::__construct($name, $title, $items);
        $this->setTemplate('forms/FrontendUploadField');
        $this->setTemplateFileEdit('forms/FrontendUploadField_FileEdit');
        $this->setTemplateFileButtons('forms/FrontendUploadField_FileButtons');
        $this->setCanAttachExisting(false); // Block access to Silverstripe assets library
        $this->setCanPreviewFolder(false); // Don't show target filesystem folder on upload field
        $this->relationAutoSetting = false; // Prevents the form thinking the GalleryPage is the underlying object
        $this->setConfig('overwriteWarning', false);
        $this->getUpload()->setReplaceFile(false);

        //the page crash if we click edit before the page is loaded
        Requirements::customCSS('.ss-uploadfield-item-edit.disabled { background:#eee; color:#666}');
        Requirements::customScript("
jQuery('.ss-uploadfield-item-edit').attr('disabled','disabled').addClass('disabled');
jQuery(window).load(function() {
	jQuery('.ss-uploadfield-item-editform').removeClass('loading'); //fix edit form in frontend
	jQuery('.ss-uploadfield-item-edit').removeAttr('disabled').removeClass('disabled');
});
");
    }

    public function attach(SS_HTTPRequest $request)
    {
        if ($this->canChooseFromGallery()) {
            // Retrieve file attributes required by front end
            $return = array();
            $files  = File::get()->byIDs($request->postVar('ids'));
            foreach ($files as $file) {
                $return[] = $this->encodeFileAttributes($file);
            }
            $response = new SS_HTTPResponse(Convert::raw2json($return));
            $response->addHeader('Content-Type', 'application/json');
            return $response;
        }
        return parent::attach($request);
    }

    /**
     * @param boolean|string $canChooseFromGallery Either a boolean flag, or a required
     * permission code
     * @return UploadField Self reference
     */
    public function setCanChooseFromGallery($canChooseFromGallery)
    {
//		$this->setCanAttachExisting($canChooseFromGallery);
        return $this->setConfig('canChooseFromGallery', $canChooseFromGallery);
    }

    /**
     * @return boolean
     */
    public function canChooseFromGallery()
    {
        if (!$this->isActive()) return false;
        $can = $this->getConfig('canChooseFromGallery');
        if ($can === null) {
            return false;
        }
        return (is_bool($can)) ? $can : Permission::check($can);
    }

    /**
     * @param string $galleryUrl
     * permission code
     * @return UploadField Self reference
     */
    public function setGalleryUrl($galleryUrl)
    {
        $this->galleryUrl = $galleryUrl;
        return $this;
    }

    /**
     * @return boolean
     */
    public function GalleryUrl()
    {
        if (!$this->galleryUrl) {
            return Controller::curr()->Link('gallery');
        }
        return $this->galleryUrl;
    }

    /**
     * @param int $itemID
     * @return UploadField_ItemHandler
     */
    public function getItemHandler($itemID)
    {
        return FrontendUploadField_ItemHandler::create($this, $itemID);
    }

    public function getUseCropbox()
    {
        return $this->useCropbox;
    }

    public function getUseFocuspoint()
    {
        return $this->useFocuspoint;
    }

    public function setUseCropbox($useCropbox)
    {
        $this->useCropbox = $useCropbox;
    }

    public function setUseFocuspoint($useFocuspoint)
    {
        $this->useFocuspoint = $useFocuspoint;
    }

    /**
     * @param SS_HTTPRequest $request
     * @return UploadField_ItemHandler
     */
    /* public function handleSelect(SS_HTTPRequest $request) {
      return FrontendUploadField_SelectHandler::create($this, $this->getFolderName());
      } */

    public function handleGallery(SS_HTTPRequest $request)
    {
        return UploadField_SelectHandler::create($this, $this->getFolderName());
    }

    public function getAttributes()
    {
        $attrs = array(
            'type' => 'text',
            'name' => $this->getName(),
            'value' => $this->Value(),
            'class' => $this->extraClass(),
            'id' => $this->ID(),
            'disabled' => $this->isDisabled(),
        );

        $attributes          = array_merge($attrs, $this->attributes);
        $attributes['name']  = $attributes['name'].'[Uploads][]';
        $attributes['class'] = $attributes['class'].' ss-uploadfield-fromcomputer-fileinput';
        $attributes['type']  = 'file';
        return $attributes;
    }

    public function getAttributesHTML($attrs = null)
    {
        if ($attrs === null) {
            $attrs = $this->getAttributes();
        }
        if (isset($attrs['value'])) {
            unset($attrs['value']);
        }
        return parent::getAttributesHTML($attrs);
    }

    /**
     * Loads the temporary file data into a File object
     *
     * @param array $tmpFile Temporary file data
     * @param string $error Error message
     * @return File File object, or null if error
     */
    protected function saveTemporaryFile($tmpFile, &$error = null)
    {
        //override with a more meaningful name
        $tmpFile['name'] = $this->name.'_'.time().'.'.strtolower(pathinfo($tmpFile['name'],
                    PATHINFO_EXTENSION));

        $file = parent::saveTemporaryFile($tmpFile, $error);

        return $file;
    }

    /**
     * Customises a file with additional details suitable for rendering in the
     * UploadField.ss template
     *
     * @param File $file
     * @return ViewableData_Customised
     */
    protected function customiseFile(File $file)
    {
        $customizedfile = $file->customise(array(
            'UploadFieldThumbnailURL' => $this->getThumbnailURLForFile($file),
            'UploadFieldDeleteLink' => $this->getItemHandler($file->ID)->DeleteLink(),
            'UploadFieldEditLink' => $this->getItemHandler($file->ID)->EditLink(),
            'UploadField' => $this,
            'OriginalFile' => $file, //the customized object has no extensions :-(
        ));

        // we do this in a second customise to have the access to the previous customisations
        return $customizedfile->customise(array(
                'UploadFieldFileButtons' => (string) $file->renderWith($this->getTemplateFileButtons())
        ));
    }

    public function getFileEditFields(File $file)
    {
        $fields = new FieldList;

        if ($this->getUseCropbox()) {
            $f       = new CropboxField(
                $name    = "Cropbox", $title   = "Crop box",
                $imageID = $file->ID
            );
            $f->addExtraClass('stacked');
            $fields->push($f);
        }

        if ($this->getUseFocuspoint()) {
            $f       = new FocusPointField(
                $name    = "FocusXY", $title   = "Focus point",
                $imageID = $file->ID
                //$value = FocusPointField::sourceCoordsToFieldValue($this->owner->FocusX,$this->owner->FocusY) //@todo $value argument isn't getting passed through for some reason
            );
            $f->setValue(FocusPointField::sourceCoordsToFieldValue($file->FocusX,
                    $file->FocusY));
            $f->addExtraClass('stacked');
            $fields->push($f);
        }
        return $fields;
    }

    public function setValue($value, $record = null)
    {
        $result = parent::setValue($value, $record);
        return $result;
    }
}

/**
 * RequestHandler for actions (edit, remove, delete) on a single item (File) of the UploadField
 *
 * @author Zauberfisch
 * @package framework
 * @subpackage forms
 */
class FrontendUploadField_ItemHandler extends UploadField_ItemHandler
{
    private static $allowed_actions = array(
        'delete',
        'edit',
        'EditForm',
    );

    /**
     * Action to handle editing of a single file
     *
     * @param SS_HTTPRequest $request
     * @return ViewableData_Customised
     */
    public function edit(SS_HTTPRequest $request)
    {
        // Check form field state
        if ($this->parent->isDisabled() || $this->parent->isReadonly())
                return $this->httpError(403);

        // Check item permissions
        $item = $this->getItem();
        if (!$item) return $this->httpError(404);


        $res = false;
        try {
            $res = $item->canEditFrontend();
        } catch (Exception $ex) {

        }

        if (!$res) return $this->httpError(403);

        Requirements::css(FRAMEWORK_DIR.'/css/UploadField.css');

        return $this->customise(array(
                'Form' => $this->EditForm()
            ))->renderWith($this->parent->getTemplateFileEdit());
    }
}