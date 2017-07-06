<?php

/**
 * AjaxUploadController
 *
 * @author Kalyptus SPRL <thomas@kalyptus.be>
 */
class AjaxUploadController extends Controller
{
     private static $allowed_actions = [
        'ckeditor'
    ];

    /**
     * @link http://docs.ckeditor.com/#!/guide/dev_file_upload
     */
    public function ckeditor()
    {
        $uploaded = 1;
        $fileName = null;
        $url = null;
        $error = 'Not implemented';

        $result = [
            'uploaded' => $uploaded,
        ];

        if ($fileName) {
            $result['fileName'] = $fileName;
        }
        if ($url) {
            $result['url'] = $url;
        }
        if ($error) {
            $result['error']['message'] = $error;
        }
        return json_encode($result);
    }
}
