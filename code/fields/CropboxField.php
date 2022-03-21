<?php

/**
 * A field that use cropbox
 */
class CropboxField extends FormField
{

    protected $cropboxWidth = 200;
    protected $cropboxHeight = 200;

    public function __construct($name = 'Cropbox', $title = 'Crop Box', $imageID = null, $value = '', $form = null)
    {
        $this->setDefaultValue();
        $this->setImage($imageID);

        parent::__construct($name, ($title === null) ? $name : $title, $value, $form);
    }

    public function setDefaultValue()
    {
        $this->value = array(
            'CropX' => 0,
            'CropY' => 0,
            'CropboxWidth' => 0,
            'CropboxHeight' => 0
        );
    }

    public function getCropboxWidth()
    {
        return $this->cropboxWidth;
    }

    public function getCropboxHeight()
    {
        return $this->cropboxHeight;
    }

    public function setCropboxWidth($cropWidth)
    {
        $this->cropboxWidth = $cropWidth;
    }

    public function setCropboxHeight($cropHeight)
    {
        $this->cropboxHeight = $cropHeight;
    }

    public function setImage($imageID)
    {
        if ($imageID) {
            $this->ImageID = $imageID;

            $image = $this->getImage();
            // It's not an image
            if (!$image) {
                return;
            }
            $this->setValue(array(
                'CropX' => $image->CropX,
                'CropY' => $image->CropY,
                'CropboxWidth' => $image->CropboxWidth,
                'CropboxHeight' => $image->CropboxHeigth
            ));
        }
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        if ($this->ImageID) {
            return Image::get()->byID($this->ImageID);
        }
    }

    public function Field($properties = array())
    {
        $image = $this->getImage();
        if (!$image) {
            return false;
        }

        $properties['CropX'] = $image->CropX;
        $properties['CropY'] = $image->CropY;

        FormExtraJquery::include_jquery();
        if (self::config()->use_hammer) {
            FormExtraJquery::include_hammer();
        }
        if (self::config()->use_mousewheel) {
            FormExtraJquery::include_mousewheel();
        }
        Requirements::javascript(FORM_EXTRAS_PATH . '/javascript/cropbox/jquery.cropbox.js');
        Requirements::css(FORM_EXTRAS_PATH . '/javascript/cropbox/jquery.cropbox.css');
        Requirements::customScript("jQuery( '.cropbox-field' ).each( function () {
			var t = jQuery(this),
			image = t.find('img'),
            cropwidth = image.data('cropwidth'),
            cropheight = image.data('cropheight'),
			x       = jQuery('[name=CropX]', t),
            y       = jQuery('[name=CropY]', t),
            w       = jQuery('[name=CropboxWidth]', t),
            h       = jQuery('[name=CropboxHeight]', t)
		;

          image.cropbox( {width: cropwidth, height: cropheight, result: {cropX:x.val(), cropY:y.val(), cropW:w.val(), cropH:h.val()} })
            .on('cropbox', function( event, results, img ) {
				x.val(results.cropX);
				y.val(results.cropY);
				w.val(results.cropW);
				h.val(results.cropH);
            })
			;
      } );", 'cropboxFieldInit');

        return parent::Field($properties);
    }
}

class CropboxImage extends DataExtension
{

    private static $db = array(
        'CropX' => 'Int',
        'CropY' => 'Int',
        'CropboxWidth' => 'Int',
        'CropboxHeight' => 'Int',
    );
    private static $defaults = array(
        'CropX' => '0',
        'CropY' => '0'
    );

    public function updateCMSFields(FieldList $fields)
    {
        $field = new CropboxField(
            $name = "Cropbox",
            $title = "Crop Box",
            $imageID = $this->owner->ID
        );
        $field->addExtraClass('stacked');

        if ($fields->hasTabSet()) {
            $fields->addFieldToTab('Root.Main', $field);
        } else {
            $fields->add($field);
        }
    }

    public function onBeforeWrite()
    {
        if (isset($_POST['CropX'])) {
            //Save coords
            $this->owner->CropX = isset($_POST['CropX']) ? (int) $_POST['CropX'] : 0;
            $this->owner->CropY = isset($_POST['CropY']) ? (int) $_POST['CropY'] : 0;
            $this->owner->CropWidth = isset($_POST['CropboxWidth']) ? (int) $_POST['CropboxWidth'] : 0;
            $this->owner->CropHeight = isset($_POST['CropboxHeight']) ? (int) $_POST['CropboxHeight'] : 0;
            //Flush images if crop has changed
            if (
                $this->owner->isChanged('CropX') || $this->owner->isChanged('CropY') || $this->owner->isChanged('CropboxWidth') || $this->owner->isChanged('CropboxHeight')
            ) {
                $this->owner->deleteFormattedImages();
            }
        }
        parent::onBeforeWrite();
    }

    /**
     * Generate a resized copy of this image with the given width & height, cropping to maintain aspect ratio and focus point.
     * Use in templates with $CropboxImage
     *
     * @param integer $width Width to crop to
     * @param integer $height Height to crop to
     * @return Image
     */
    public function CropboxedImage($width, $height)
    {
        return $this->owner->getFormattedImage('CropboxedImage', $width, $height);
    }

    /**
     * Generate a resized copy of this image with the given width & height, cropping to maintain aspect ratio and focus point.
     * Use in templates with $CropboxImage
     *
     * @param GD|GDBackend $gd
     * @param integer $width Width to crop to
     * @param integer $height Height to crop to
     * @return GD|GDBackend
     */
    public function generateCropboxedImage($gd, $width, $height)
    {
        $width = round($width);
        $height = round($height);

        // Check that a resize is actually necessary.
        if ($width == $this->owner->Width && $height == $this->owner->Height) {
            return $this;
        }

        if ($this->owner->Width > 0 && $this->owner->Height > 0 && $this->owner->CropWidth > 0 && $this->owner->CropHeight > 0) {
            return $gd->crop($this->owner->CropY, $this->owner->CropX, $this->owner->CropWidth, $this->owner->CropHeight)->resize($width, $height);
        } else {
            return $gd->croppedResize($width, $height);
        }
    }

    public function writeTo($path)
    {
    }
}
