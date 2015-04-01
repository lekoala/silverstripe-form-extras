<?php

/**
 * Export a filtered selection of records insteand of the current list. Export all by default
 */
class GridFieldExportFilteredButton extends GridFieldExportButton
{
    protected $btnTitle   = null;
    protected $actionName = 'export';
    protected $filter     = array();
    protected $exclude    = array();
    protected $filterAny  = array();
    protected $where      = null;

    public function getWhere()
    {
        return $this->where;
    }

    public function setWhere($where)
    {
        $this->where = $where;
        return $this;
    }

    public function getFilterAny()
    {
        return $this->filterAny;
    }

    public function setFilterAny($filterAny)
    {
        $this->filterAny = $filterAny;
        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    public function getExclude()
    {
        return $this->exclude;
    }

    public function setExclude($exclude)
    {
        $this->exclude = $exclude;
        return $this;
    }

    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * Set the action name to something else to allow multiple export buttons
     *
     * @param string $actionName Something like "export_myexport"
     * @return \GridFieldExportAllButton
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
        return $this;
    }

    public function getBtnTitle()
    {
        if (!$this->btnTitle) {
            return _t('GridFieldExportAllButton.LABEL', 'Export all to CSV');
        }
        return $this->btnTitle;
    }

    public function setBtnTitle($v)
    {
        $this->btnTitle = $v;
    }

    /**
     * export is an action button
     */
    public function getActions($gridField)
    {
        return array($this->actionName);
    }

    public function handleAction(GridField $gridField, $actionName, $arguments,
                                 $data)
    {
        if ($actionName == $this->actionName) {
            return $this->handleExport($gridField);
        }
    }

    /**
     * it is also a URL
     */
    public function getURLHandlers($gridField)
    {
        return array(
            $this->actionName => 'handleExport',
        );
    }

    /**
     * Place the export button in a <p> tag below the field
     */
    public function getHTMLFragments($gridField)
    {
        $button = new GridField_FormAction(
            $gridField, $this->actionName, $this->getBtnTitle(),
            $this->actionName, null
        );
        $button->setAttribute('data-icon', 'download-csv');
        $button->addExtraClass('no-ajax');
        return array(
            $this->targetFragment => '<p class="grid-csv-button">'.$button->Field().'</p>',
        );
    }

    /**
     * Generate export fields for CSV.
     *
     * @param GridField $gridField
     * @return array
     */
    public function generateExportFileData($gridField)
    {
        $separator  = $this->csvSeparator;
        $csvColumns = ($this->exportColumns) ? $this->exportColumns : singleton($gridField->getModelClass())->summaryFields();
        $fileData   = '';
        $columnData = array();
        $fieldItems = new ArrayList();

        if ($this->csvHasHeader) {
            $headers = array();

            // determine the CSV headers. If a field is callable (e.g. anonymous function) then use the
            // source name as the header instead
            foreach ($csvColumns as $columnSource => $columnHeader) {
                $headers[] = (!is_string($columnHeader) && is_callable($columnHeader))
                        ? $columnSource : $columnHeader;
            }

            $fileData .= "\"".implode("\"{$separator}\"", array_values($headers))."\"";
            $fileData .= "\n";
        }


        $items = $gridField->getList();

        // Apply filters for a datalist
        if ($items instanceof DataList) {
            if (!empty($this->filter)) {
                $items = $items->filter($this->filter);
            }
            if (!empty($this->exclude)) {
                $items = $items->exclude($this->exclude);
            }
            if (!empty($this->filterAny)) {
                $items = $items->filterAny($this->filterAny);
            }
            if (!empty($this->where)) {
                $items = $items->where($this->where);
            }
        }

        foreach ($items as $item) {
            $columnData = array();
            foreach ($csvColumns as $columnSource => $columnHeader) {
                if (!is_string($columnHeader) && is_callable($columnHeader)) {
                    if ($item->hasMethod($columnSource)) {
                        $relObj = $item->{$columnSource}();
                    } else {
                        $relObj = $item->relObject($columnSource);
                    }

                    $value = $columnHeader($relObj);
                } else {
                    $value = $gridField->getDataFieldValue($item, $columnSource);
                }

                $value        = str_replace(array("\r", "\n"), "\n", $value);
                $columnData[] = '"'.str_replace('"', '\"', $value).'"';
            }
            $fileData .= implode($separator, $columnData);
            $fileData .= "\n";

            $item->destroy();
        }

        return $fileData;
    }
}