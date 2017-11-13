<?php

/**
 * ColumnHolderField
 *
 * @author Kalyptus SPRL <thomas@kalyptus.be>
 */
class ColumnHolderField extends CompositeField
{

    protected $leftCol;
    protected $rightCol;
    protected $widths;

    public function __construct($children = null)
    {
        parent::__construct($children);
        $this->addExtraClass('col-holder clearfix');
        $this->widths = array(6, 6);
    }

    public function getWidths()
    {
        return $this->widths;
    }

    public function setWidths($left, $right)
    {
        $this->widths = array($left, $right);
    }

    public function getColClass($pos)
    {
        $width = $this->widths[$pos];
        $theme = self::config()->class_theme;
        switch ($theme) {
            case 'bootstrap3':
                return 'col-md-' . $width;
            default:
                return 'col-' . $width;
        }
    }

    /**
     *
     * @return ColumnField
     */
    public function getLeftCol()
    {
        if (!$this->leftCol) {
            $this->leftCol = new ColumnField('left');
            $this->leftCol->addExtraClass($this->getColClass(0));
            $this->push($this->leftCol);
        }
        return $this->leftCol;
    }

    /**
     *
     * @return ColumnField
     */
    public function getRightCol()
    {
        if (!$this->rightCol) {
            $this->rightCol = new ColumnField('right');
            $this->rightCol->addExtraClass($this->getColClass(1));
            $this->push($this->rightCol);
        }
        return $this->rightCol;
    }
}
