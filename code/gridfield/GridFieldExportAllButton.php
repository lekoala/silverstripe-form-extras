<?php

class GridFieldExportAllButton extends GridFieldExportButton
{

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