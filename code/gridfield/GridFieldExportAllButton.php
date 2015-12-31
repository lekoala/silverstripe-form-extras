<?php

/**
 * Export all records from the table instead of the current list
 */
class GridFieldExportAllButton extends GridFieldExportButton
{
    /**
     * @var string
     */
    protected $csvSeparator = ";";

    /**
     * Place the export button in a <p> tag below the field
     */
    public function getHTMLFragments($gridField)
    {
        $button = new GridField_FormAction(
            $gridField, 'export_all',
            _t('GridFieldExportAllButton.LABEL', 'Export all to CSV'),
            'export_all', null
        );
        $button->setAttribute('data-icon', 'download-csv');
        $button->addExtraClass('no-ajax');
        return array(
            $this->targetFragment => '<p class="grid-csv-button">'.$button->Field().'</p>',
        );
    }

    /**
     * export is an action button
     */
    public function getActions($gridField)
    {
        return array('export_all');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments,
                                 $data)
    {
        if ($actionName == 'export_all') {
            return $this->handleExport($gridField);
        }
    }

    /**
     * it is also a URL
     */
    public function getURLHandlers($gridField)
    {
        return array(
            'export_all' => 'handleExport',
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
        $separator = $this->csvSeparator;
        $singl     = singleton($gridField->getModelClass());
        if ($singl->hasMethod('exportedFields')) {
            $fallbackColumns = $singl->exportedFields();
        } else {
            $fallbackColumns = $singl->summaryFields();
        }
        $csvColumns = ($this->exportColumns) ? $this->exportColumns : $fallbackColumns;
        $fileData   = '';
        $columnData = array();
        $fieldItems = new ArrayList();

        if ($this->csvHasHeader) {
            $headers = array();

            // determine the CSV headers. If a field is callable (e.g. anonymous function) then use the
            // source name as the header instead
            foreach ($csvColumns as $columnSource => $columnHeader) {
                $headers[] = (!is_string($columnHeader) && is_callable($columnHeader))
                        ? utf8_decode($columnSource) : utf8_decode($columnHeader);
            }

            $fileData .= "\"".implode("\"{$separator}\"", array_values($headers))."\"";
            $fileData .= "\n";
        }

        $items = $gridField->getList();

        foreach ($items as $item) {
            if (!$item->hasMethod('canView') || $item->canView()) {
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
                        $value = $gridField->getDataFieldValue($item,
                            $columnSource);

                        if (!$value) {
                            $value = $gridField->getDataFieldValue($item,
                                $columnHeader);
                        }
                    }

                    $value        = str_replace(array("\r", "\n"), "\n", $value);
                    $columnData[] = '"'.str_replace('"', '""', utf8_decode($value)).'"';
                }

                $fileData .= implode($separator, $columnData);
                $fileData .= "\n";
            }

            if ($item->hasMethod('destroy')) {
                $item->destroy();
            }
        }

        return $fileData;
    }
}
