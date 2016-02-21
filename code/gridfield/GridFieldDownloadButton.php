<?php

class GridFieldDownloadButton implements GridField_ColumnProvider, GridField_ActionProvider
{

    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return array('class' => 'col-buttons');
    }

    public function getColumnMetadata($gridField, $columnName)
    {
        if ($columnName == 'Actions') {
            return array('title' => '');
        }
    }

    public function getColumnsHandled($gridField)
    {
        return array('Actions');
    }

    public function getColumnContent($gridField, $record, $columnName)
    {
        if ($record->FileID) {
            return "<a class='action' href='".Controller::curr()->Link(get_class($record) . '/download?fileID=' . $record->ID) ."'>"._t('GridFieldDownloadButton.DOWLOAD', 'Download')."</a>";

            /*$field = GridField_FormAction::create(
                            $gridField, 'CustomAction' . $record->ID, _t('GridFieldDownloadButton.DOWLOAD', 'Download'), "doDownload", array(
                        'RecordID' => $record->ID,
                        'FileID' => $record->FileID
                            )
            );

            return $field->Field();*/
        }
        return '';
    }

    public function getActions($gridField)
    {
        return array('doDownload');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if (isset($arguments['FileID'])) {
            $response = Controller::curr()->getResponse();

            $file = File::get()->byID($arguments['FileID']);
            $path = $file->getFullPath();
            $filename = $file->getFileName();
            return SS_HTTPRequest::send_file(file_get_contents($path), $filename);
        }
        Controller::curr()->getResponse()->setStatusCode(
                200, 'No File'
        );
    }
}
