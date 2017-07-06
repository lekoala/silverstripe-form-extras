<?php

/**
 * AjaxUploadController
 *
 * @author Kalyptus SPRL <thomas@kalyptus.be>
 */
class AjaxUploadController extends Controller
{

    const CKEDITOR_TYPE_IMAGES = 'Images';

    private static $allowed_actions = [
        'ckeditor'
    ];

    /**
     * @link http://docs.ckeditor.com/#!/guide/dev_file_upload
     */
    public function ckeditor()
    {
        $errorFn = function($msg) {
            return json_encode(['uploaded' => 0, 'error' => ['message' => $msg]]);
        };

        $request = $this->getRequest();

        $token = $request->getHeader('X-Securitytoken');
        $SecurityToken = SecurityToken::inst();
        if ($SecurityToken->isEnabled() && !$token) {
            return $errorFn('No token');
        }
        if (!$SecurityToken->check($token)) {
            return $errorFn('Invalid token');
        }

        if (empty($_FILES['upload'])) {
            return $errorFn('No file uploaded');
        }

        $type = $request->getVar('type');

        /* @var $file Image|File */
        $file = null;
        if ($type && $type == self::CKEDITOR_TYPE_IMAGES) {
            $file = Image::create();
        } else {
            $file = File::create();
        }

        $tmpFile = $_FILES['upload'];

        $folder = 'TemporaryUploads';

        try {
            $upload = Upload::create()->loadIntoFile($tmpFile, $file, $folder);
            $file->write();
        } catch (ValidationException $e) {
            return $errorFn(_t('AjaxUploadController.UPLOADVALIDATIONFAIL', 'Unallowed file uploaded'));
        }

        $result = [
            'uploaded' => 1,
            'fileName' => basename($file->getFilename()),
            'url' => $file->getAbsoluteURL(),
        ];
        return json_encode($result);
    }
}
