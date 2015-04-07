<?php

/**
 * A field that use cropbox
 */
class CropboxField extends FormField
{
    protected $cropWidth  = 200;
    protected $cropHeight = 200;

    public function __construct($name = 'Cropbox', $title = 'Crop Box',
                                $imageID = null, $value = '', $form = null)
    {
        $this->setDefaultValue();
        $this->setImage($imageID);

        FormExtraJquery::include_jquery();
        if (self::config()->use_hammer) {
            FormExtraJquery::include_hammer();
        }
        if (self::config()->use_mousewheel) {
            FormExtraJquery::include_mousewheel();
        }
        Requirements::javascript(FORM_EXTRAS_PATH.'/javascript/cropbox/jquery.cropbox.js');
        Requirements::css(FORM_EXTRAS_PATH.'/javascript/cropbox/jquery.cropbox.css');
        Requirements::customScript("jQuery( '.cropbox-field' ).each( function () {
			var t = jQuery(this),
			image = t.find('img'),
            cropwidth = image.data('cropwidth'),
            cropheight = image.data('cropheight'),
			x       = jQuery('[name=CropX]', t),
            y       = jQuery('[name=CropY]', t),
            w       = jQuery('[name=CropWidth]', t),
            h       = jQuery('[name=CropHeight]', t),
            p       = jQuery('[name=CropPercent]', t),
            l       = jQuery('[name=CropLeft]', t),
            t       = jQuery('[name=CropTop]', t)
		;

          image.cropbox( {width: cropwidth, height: cropheight, percent: p.val(), left: l.val(), top: t.val() } )
            .on('cropbox', function( event, results, img ) {
				x.val(results.cropX);
				y.val(results.cropY);
				w.val(results.cropW);
				h.val(results.cropH);
				p.val(results.percent);
				l.val(results.left);
				t.val(results.top);
            })
			;
      } );");



        parent::__construct($name, ($title === null) ? $name : $title, $value,
            $form);
    }

    public function setDefaultValue()
    {
        $this->value = array(
            'CropX' => 0,
            'CropY' => 0,
            'CropWidth' => 0,
            'CropHeight' => 0
        );
    }

    public function getCropWidth()
    {
        return $this->cropWidth;
    }

    public function getCropHeight()
    {
        return $this->cropHeight;
    }

    public function setCropWidth($cropWidth)
    {
        $this->cropWidth = $cropWidth;
    }

    public function setCropHeight($cropHeight)
    {
        $this->cropHeight = $cropHeight;
    }

    public function setImage($imageID)
    {
        if ($imageID) {
            $this->ImageID = $imageID;

            $image = $this->getImage();
            $this->setValue(array(
                'CropX' => $image->CropX,
                'CropY' => $image->CropY,
                'CropWidth' => $image->CropWidth,
                'CropHeight' => $image->CropHeigth
            ));
        }
    }

    public function getImage()
    {
        if ($this->ImageID) {
            return Image::get()->byID($this->ImageID);
        }
    }
}

class CropboxImage extends DataExtension
{
    private static $db       = array(
        'CropX' => 'Int',
        'CropY' => 'Int',
        'CropWidth' => 'Int',
        'CropHeight' => 'Int',
        //the three following are required to restore plugin to original state
        'CropPercent' => 'Varchar(255)',
        'CropLeft' => 'Varchar',
        'CropTop' => 'Varchar',
    );
    private static $defaults = array(
        'CropX' => '0',
        'CropY' => '0'
    );

    public function updateCMSFields(FieldList $fields)
    {
        //Add FocusPoint field for selecting focus
        $f       = new CropboxField(
            $name    = "Cropbox", $title   = "Crop Box",
            $imageID = $this->owner->ID
        );
        $fields->addFieldToTab("Root.Main", $f);
    }

    public function onBeforeWrite()
    {
        if (isset($_POST['CropX'])) {
            //Save coords
            $this->owner->CropX       = isset($_POST['CropX']) ? (int) $_POST['CropX']
                    : 0;
            $this->owner->CropY       = isset($_POST['CropX']) ? (int) $_POST['CropY']
                    : 0;
            $this->owner->CropWidth   = isset($_POST['CropX']) ? (int) $_POST['CropWidth']
                    : 0;
            $this->owner->CropHeight  = isset($_POST['CropX']) ? (int) $_POST['CropHeight']
                    : 0;
            $this->owner->CropPercent = isset($_POST['CropPercent']) ? (float) $_POST['CropPercent']
                    : 0;
            $this->owner->CropLeft    = isset($_POST['CropLeft']) ? Convert::raw2sql($_POST['CropLeft'])
                    : '';
            $this->owner->CropTop     = isset($_POST['CropTop']) ? Convert::raw2sql($_POST['CropTop'])
                    : '';
            //Flush images if crop has changed
            if ($this->owner->isChanged('CropX') || $this->owner->isChanged('CropY')
                || $this->owner->isChanged('CropWidth') || $this->owner->isChanged('CropHeight')
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
     * @param GD $gd
     * @param integer $width Width to crop to
     * @param integer $height Height to crop to
     * @return GD
     */
    public function generateCropboxedImage(GD $gd, $width, $height)
    {

        $width  = round($width);
        $height = round($height);

        // Check that a resize is actually necessary.
        if ($width == $this->owner->width && $height == $this->owner->height) {
//			return $this;
        }

        if ($this->owner->width > 0 && $this->owner->height > 0 && $this->owner->CropWidth
            > 0 && $this->owner->CropHeight > 0) {
            return $gd->crop($this->owner->CropY, $this->owner->CropX,
                    $this->owner->CropWidth, $this->owner->CropHeight)->resize($width,
                    $height);
        } else {
            return $gd->croppedResize($width, $height);
        }
    }
}