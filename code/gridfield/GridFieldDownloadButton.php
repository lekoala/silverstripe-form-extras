<?php

class GridFieldDownloadButton implements GridField_ColumnProvider, GridField_ActionProvider {

	public function augmentColumns($gridField, &$columns) {
		if (!in_array('Actions', $columns)) {
			$columns[] = 'Actions';
		}
	}

	public function getColumnAttributes($gridField, $record, $columnName) {
		return array('class' => 'col-buttons');
	}

	public function getColumnMetadata($gridField, $columnName) {
		if ($columnName == 'Actions') {
			return array('title' => '');
		}
	}

	public function getColumnsHandled($gridField) {
		return array('Actions');
	}

	public function getColumnContent($gridField, $record, $columnName) {
		if ($record->FileID) {
			return "<a class='action' href='".Controller::curr()->Link(get_class($record) . '/download?fileID=' . $record->ID) ."'>"._t('GridFieldDownloadButton.DOWLOAD','Download')."</a>";
			
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

	public function getActions($gridField) {
		return array('doDownload');
	}

	public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
		if (isset($arguments['FileID'])) {
			$response = Controller::curr()->getResponse();
			
			$file = File::get()->byID($arguments['FileID']);
			$path = $file->getFullPath();
			$filename = $file->getFileName();
//			header('Content-Transfer-Encoding: binary');  // For Gecko browsers mainly
//			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
//			header('Accept-Ranges: bytes');  // For download resume
//			header('Content-Length: ' . filesize($path));  // File size
//			header('Content-Encoding: none');
//			header('Content-Type: application/pdf');  // Change this mime type if the file is not PDF
//			header('Content-Disposition: attachment; filename=' . $filename);  // Make the browser display the Save As dialog
//			readfile($path);
			
//			$response->setBody(file_get_contents($path));
//			$response->addHeader('X-Refresh',true);
//			$response->addHeader('Content-Transfer-Encoding','binary');
//			$response->addHeader('Last-Modified',gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
//			$response->addHeader('Accept-Ranges','bytes');
//			$response->addHeader('Content-Length',filesize($path));
//			$response->addHeader('Content-Encoding','none');
//			$response->addHeader('Content-Disposition','filename=' . $filename);
			
			return SS_HTTPRequest::send_file(file_get_contents($path), $filename);
		}
		Controller::curr()->getResponse()->setStatusCode(
				200, 'No File'
		);
	}

}
