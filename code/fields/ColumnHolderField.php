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

    public function __construct($children = null)
    {
        parent::__construct($children);
        $this->addExtraClass('col-holder clearfix');
    }

    /**
     *
     * @return ColumnField
     */
    public function getLeftCol()
    {
        if (!$this->leftCol) {
            $this->leftCol = new ColumnField('left');
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
            $this->push($this->rightCol);
        }
        return $this->rightCol;
    }
}
