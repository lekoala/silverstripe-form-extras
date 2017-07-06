<?php

/**
 * AjaxUploadController
 *
 * @author Kalyptus SPRL <thomas@kalyptus.be>
 */
class AjaxUploadController extends Controller
{

    const TEMPORARY_FOLDER = 'TemporaryUploads';
    const CKEDITOR_TYPE_IMAGES = 'Images';

    private static $allowed_actions = [
        'ckeditor',
        'oembed',
    ];

    public function oembed()
    {
        $request = $this->getRequest();

        $url = $request->getVar('embed_url');
        $callback = $request->getVar('callback');

        $embed = Embed\Embed::create($url);

        $code = $embed->getCode();
        $type = $embed->getType();

        if ($type == 'video') {
            $code = '<div class="embed-container">' . $code . '</div>';
        }

        $data = [
            'html' => $code,
            'type' => $type,
            'width' => $embed->getWidth(),
            'height' => $embed->getHeight(),
            'author_name' => $embed->getAuthorName(),
            'author_url' => $embed->getAuthorUrl(),
            'version' => '1.0',
            'provider_url' => $embed->getProviderUrl(),
            'provider_name' => $embed->getProviderName(),
            'thumbnail_width' => $embed->getImageWidth(),
            'thumbnail_height' => $embed->getImageHeight(),
            'thumbnail_url' => $embed->getImage(),
            'url' => $embed->getUrl(),
            'title' => $embed->getTitle(),
            'description' => $embed->getDescription(),
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT);

        $this->getResponse()->addHeader('Content-type', 'text/javascript');

        if ($callback) {
            return "$callback && $callback($json);";
        }
        return $json;
    }

    /**
     * @link http://docs.ckeditor.com/#!/guide/dev_file_upload
     */
    public function ckeditor()
    {
        $errorFn = function($msg) {
            return json_encode(['uploaded' => 0, 'error' => ['message' => $msg]]);
        };

        $request = $this->getRequest();

        $token = $request->getHeader('X-Csrf');
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

        // You will have to move these files outside of this folder afterwards
        $folder = self::TEMPORARY_FOLDER;

        try {
            $upload = Upload::create()->loadIntoFile($tmpFile, $file, $folder);
            $file->OwnerID = Member::currentUserID();
            $file->write();
        } catch (ValidationException $e) {
            return $errorFn(_t('AjaxUploadController.UPLOADVALIDATIONFAIL', 'Unallowed file uploaded'));
        }

        $result = [
            'uploaded' => 1,
            'fileName' => basename($file->getFilename()),
            'url' => $file->getURL(),
        ];
        return json_encode($result);
    }

    /**
     * Finds temporary file and image in a given html content
     *
     * @param string $content Your html content
     * @return array An array of files
     */
    public static function findTemporaryUploads($content)
    {
        $matches = null;
        preg_match_all('/(?:href|src)=\"([^\"]+)/', $content, $matches);

        $files = [];

        if (empty($matches[1])) {
            return $files;
        }

        foreach ($matches[1] as $match) {
            $strpos = strpos($match, '/' . self::TEMPORARY_FOLDER . '/');
            if ($strpos === false) {
                continue;
            }

            $path = substr($match, $strpos);

            $file = File::find(ASSETS_DIR . $path);

            if ($file) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Move temporary files into a valid folder
     *
     * @param string $content
     * @param string $destFolder
     * @param array $tmpFiles
     * @return string Updated html content with new urls
     */
    public static function moveTemporaryUploads($content, $destFolder, &$tmpFiles)
    {
        $replace = [];

        $folder = Folder::find_or_make($destFolder);

        /* @var $file File */
        foreach ($tmpFiles as $file) {

            $oldURL = $file->getURL();

            $file->ParentID = $folder->ID;
            $file->write();

            $replace[$oldURL] = $file->getURL();
        }

        $content = str_replace(array_keys($replace), array_values($replace), $content);

        return $content;
    }
}
