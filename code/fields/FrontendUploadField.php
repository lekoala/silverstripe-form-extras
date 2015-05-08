<?php

/**
 * FrontendUploadField
 *
 * An upload field ready to use in a front end context
 * - Prevent access to cms files
 * - Add a gallery button for preselected images
 * - Enable editing on the front end with focuspoint or cropzoom
 *
 * Please note that to enable front end edition, the method canEditFrontend on the image
 * must return true
 *
 * @link http://doc.silverstripe.org/framework/en/trunk/reference/uploadfield (broken)
 * @link http://api.silverstripe.org/master/class-UploadField.html
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

        // Update templates to avoid cms styles
        $this->setTemplate('forms/FrontendUploadField');
        $this->setTemplateFileEdit('forms/FrontendUploadField_FileEdit');
        $this->setTemplateFileButtons('forms/FrontendUploadField_FileButtons');
        $this->setDownloadTemplateName('ss-frontenduploadfield-downloadtemplate');
        $this->setUploadTemplateName('ss-frontenduploadfield-uploadtemplate');

        // Configure to something more bullet proof
        $this->setCanAttachExisting(false); // Block access to Silverstripe assets library
        $this->setCanPreviewFolder(false); // Don't show target filesystem folder on upload field
        $this->relationAutoSetting = false; // Prevents the form thinking the GalleryPage is the underlying object
        $this->setConfig('overwriteWarning', false);
        $this->getUpload()->setReplaceFile(false);

        //the page crash if we click edit before the page is loaded
        Requirements::customCSS('.ss-uploadfield-item-edit.disabled { background:#eee; color:#666}');
        Requirements::customScript("jQuery('.ss-uploadfield-item-edit').attr('disabled','disabled').addClass('disabled');
jQuery(window).load(function() {
	jQuery('.ss-uploadfield-item-editform').removeClass('loading'); //fix edit form in frontend
	jQuery('.ss-uploadfield-item-edit').removeAttr('disabled').removeClass('disabled');
});
", "FrontendUploadFieldFix");
    }

    public function Field($properties = array())
    {
        $res = parent::Field($properties);
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/uploadfield/FrontendUploadField_downloadtemplate.js');
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/uploadfield/FrontendUploadField_uploadtemplate.js');

        return $res;
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
     * @param boolean|string $canChooseFromGallery Either a boolean flag, or a required permission code
     * @return UploadField Self reference
     */
    public function setCanChooseFromGallery($canChooseFromGallery = true)
    {
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
     * Set the url from where gallery items are loaded
     * 
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
     * Get gallery items url. Default to action "gallery" on current controller if none set
     * @return string
     */
    public function GalleryUrl()
    {
        if (!$this->galleryUrl) {
            return Controller::curr()->Link('gallery');
        }
        return $this->galleryUrl;
    }

    /**
     * Override default item handler
     * 
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

    public function setUseCropbox($useCropbox = true)
    {
        $this->useCropbox = $useCropbox;
    }

    public function getUseFocuspoint()
    {
        return $this->useFocuspoint;
    }

    public function setUseFocuspoint($useFocuspoint = true)
    {
        $this->useFocuspoint = $useFocuspoint;
    }

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
        // Override with a more meaningful name
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
            'UploadField' => $this
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

    public function UikitIcons()
    {
        if (!class_exists('ThemePageControllerExtension')) {
            return;
        }
        $c = ThemePageControllerExtension::config()->uikit;
        if ($c && !empty($c['enabled'])) {
            return true;
        }
    }

    public function IconUpload()
    {
        if ($this->UikitIcons()) {
            return 'uk-icon-upload';
        }
        return 'icon-upload';
    }

    public function IconEdit()
    {
        if ($this->UikitIcons()) {
            return 'uk-icon-pencil';
        }
        return 'icon-pencil';
    }

    public function IconRemove()
    {
        if ($this->UikitIcons()) {
            return 'uk-icon-remove';
        }
        return 'icon-remove';
    }

    public function IconPicture()
    {
        if ($this->UikitIcons()) {
            return 'uk-icon-picture-o';
        }
        return 'icon-picture';
    }
}

/**
 * RequestHandler for actions (edit, remove, delete) on a single item (File) of the UploadField
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