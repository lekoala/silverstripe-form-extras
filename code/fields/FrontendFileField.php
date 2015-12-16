<?php

/**
 * Description of FrontendFileField
 *
 * @author Koala
 */
class FrontendFileField extends FileField
{
    protected $preview;
    protected $fileID;

    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);
        $upload = new FrontendUpload();
        $upload->setName($name);
        $this->setUpload($upload);
    }

    public function setName($name)
    {
        parent::setName($name);
        $upload = $this->upload;
        if ($upload && $upload instanceof FrontendUpload) {
            $upload->setName($name);
        }
    }

    public function getFileID()
    {
        return $this->fileID;
    }

    public function setFileID($fileID)
    {
        $this->fileID = $fileID;
    }

    /**
     * @return File
     */
    public function getFile() {
        return File::get()->byID($this->fileID);
    }

    protected function processValue($value)
    {
        if (is_array($value) && !empty($value['Files'])) {
            $this->fileID = $value['Files'][0];
        }
        return $value;
    }

    public function setValue($value, $data = null)
    {
        if ($value) {
            $this->processValue($value);
        } else if ($data) {
            if (is_array($data)) {
                if (isset($data[$this->name])) {
                    $this->processValue($data[$this->name]);
                }
            } else {
                $field = $this->name . 'ID';
                if($data->$field) {
                    $this->fileID = $data->$field;
                }
            }
        }
        return parent::setValue($value);
    }

    /**
     * @return boolean
     */
    public function PreviewAvailable()
    {
        if ($this->preview && $this->fileID && $this->getFile() && $this->getFile()->exists()) {
            return true;
        }
        return false;
    }

    public function IsImage() {
        return $this->getFile() instanceof Image;
    }

    public function getPreview()
    {
        return $this->preview;
    }

    public function setPreview($preview)
    {
        $this->preview = (bool) $preview;
    }

    /**
     * Returns list of extensions allowed by this field, or an empty array
     * if there is no restriction
     *
     * @return array
     */
    public function getAllowedExtensions()
    {
        return $this->getValidator()->getAllowedExtensions();
    }

    /**
     * Limit allowed file extensions. Empty by default, allowing all extensions.
     * To allow files without an extension, use an empty string.
     * See {@link File::$allowed_extensions} to get a good standard set of
     * extensions that are typically not harmful in a webserver context.
     * See {@link setAllowedMaxFileSize()} to limit file size by extension.
     *
     * @param array $rules List of extensions
     * @return UploadField Self reference
     */
    public function setAllowedExtensions($rules)
    {
        $this->getValidator()->setAllowedExtensions($rules);
        return $this;
    }
}

class FrontendUpload extends Upload
{
    protected $name;

    function getName()
    {
        return $this->name;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Save an file passed from a form post into this object.
     * File names are filtered through {@link FileNameFilter}, see class documentation
     * on how to influence this behaviour.
     *
     * @param $tmpFile array Indexed array that PHP generated for every file it uploads.
     * @param $folderPath string Folder path relative to /assets
     * @return Boolean|string Either success or error-message.
     */
    public function load($tmpFile, $folderPath = false)
    {
        if ($tmpFile && is_array($tmpFile)) {
            // Override user name by a generic name
            $tmpFile['name'] = $this->name.'_'.time().'.'.strtolower(pathinfo($tmpFile['name'],
                        PATHINFO_EXTENSION));
        }
        return parent::load($tmpFile, $folderPath);
    }
}