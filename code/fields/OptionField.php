<?php

/**
 * OptionField
 *
 * @author lekoala
 */
class OptionField extends CheckboxField
{

    protected $baseValue;
    protected $baseName;
    protected $image;
    protected $imageWidth = 220;
    protected $imageHeight = 100;
    protected static $count_cache = array();

    public function __construct($name, $title = null, $baseValue = null, $value = null)
    {
        if (!isset(self::$count_cache[$name])) {
            self::$count_cache[$name] = array();
        }
        self::$count_cache[$name][] = $this;
        $this->baseName = $name;
        $count = count(self::$count_cache[$name]);
        $this->baseValue = $count;
        if ($baseValue) {
            $this->baseValue = $baseValue;
        }
        $name .= '_' . $count;
        parent::__construct($name, $title, $value);
    }

    public static function clearCountCache()
    {
        self::$count_cache = array();
    }

    public function Title()
    {
        if ($this->Image()) {
            return $this->ImageTag();
        }
        return parent::Title();
    }

    public function ImageWidth()
    {
        return $this->imageWidth;
    }

    public function setImageWidth($v)
    {
        $this->imageWidth = (int) $v;
        return $this;
    }

    public function ImageHeight()
    {
        return $this->imageHeight;
    }

    public function setImageHeight($v)
    {
        $this->imageHeight = (int) $v;
        return $this;
    }

    public function Image()
    {
        return $this->image;
    }

    public function ImageTag()
    {
        if (is_numeric($this->Image())) {
            return Image::get()->byID($this->Image())->SetSize($this->imageWidth, $this->imageHeight)->getTag();
        }
        return $this->image;
    }

    public function setImage($img)
    {
        $this->image = $img;
        return $this;
    }

    public function setBaseValue($v)
    {
        $this->baseValue = $v;
        return $this;
    }

    public function BaseValue()
    {
        return $this->baseValue;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function dataValue()
    {
        return ($this->value) ? $this->value : null;
    }

    public function Value()
    {
        return ($this->value) ? $this->value : null;
    }

    public function getAttributes()
    {
        $attrs = parent::getAttributes();

        return array_merge(
                $attrs, array(
            'checked' => ($this->Value() == $this->BaseValue()) ? 'checked' : null,
            'type' => 'radio',
            'name' => $this->baseName,
            'value' => $this->BaseValue()
                )
        );
    }
}
