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

        $request = $this->getRequest();

        echo '<pre>';print_r($_GET);die();
        // Required: anonymous function reference number as explained above.
        $callback = $request->getVar('CKEditorFuncNum');
        // Optional: instance name (might be used to load a specific configuration file or anything else).
        $CKEditor = $request->getVar('CKEditor');
        // Optional: might be used to provide localized messages.
        $langCode = $request->getVar('langCode');
        // Optional: compare it with the value of `ckCsrfToken` sent in a cookie to protect your server side uploader against CSRF.
        // Available since CKEditor 4.5.6.
        $ckCsrfToken = $request->postVar('ckCsrfToken');

        // Depending on the upload context, we return a json or script response
        $errorFn = function ($msg) use ($callback) {
            if (Director::is_ajax()) {
                return json_encode(['uploaded' => 0, 'error' => ['message' => $msg]]);
            }

            return "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(" . $callback . ",  \"\", \"" . $msg . "\" );</script>";
        };

        // Security token is either in the header or in passed as get
        // * We don't use the token provided by ckeditor
        $token = $request->getHeader('X-Csrf');
        if (!$token) {
            $token = $request->getVar('SecurityID');
        }
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

        $phpFileUploadErrors = array(
            0 => 'There is no error, the file uploaded with success',
            1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk.',
            8 => 'A PHP extension stopped the file upload.',
        );

        if (!empty($tmpFile['error'])) {
            return $errorFn($phpFileUploadErrors[$tmpFile['error']]);
        }

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

        if (Director::is_ajax()) {
            return json_encode($result);
        }

        return "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(" . $callback . ",  \"" . $result['url'] . "\", \"\" );</script>";
    }

    /**
     * Finds external links in a given html content
     *
     * @param string $content Your html content
     * @return array An array of files
     */
    public static function findExternalLinks($content)
    {
        $matches = null;
        preg_match_all('/(?:href|src)=\"([^\"]+)/', $content, $matches);

        if (empty($matches[1])) {
            return [];
        }

        return $matches[1];
    }

    /**
     * Finds temporary file and image in a given html content
     *
     * @param string $content Your html content
     * @return array An array of files
     */
    public static function findTemporaryUploads($content)
    {
        $links = self::findExternalLinks($content);

        if (empty($links)) {
            return $links;
        }

        $files = [];

        foreach ($links as $link) {
            $strpos = strpos($link, '/' . self::TEMPORARY_FOLDER . '/');
            if ($strpos === false) {
                continue;
            }

            $path = substr($link, $strpos);

            $file = File::find(ASSETS_DIR . $path);

            if ($file) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Diff original and new content to find and delete removed files
     *
     * @param string $originalContent
     * @param string $content
     * @return array An array of deleted files
     */
    public static function deleteUnusedFiles($originalContent, $content)
    {
        $originalFiles = self::findExternalLinks($originalContent);

        $deleted = [];

        if (empty($originalFiles)) {
            return $deleted;
        }

        $currentFiles = self::findExternalLinks($content);

        $diff = array_diff($originalFiles, $currentFiles);
        if (empty($diff)) {
            return $deleted;
        }

        foreach ($diff as $path) {
            // Skip absolute path
            if (strpos($path, 'http') === 0) {
                continue;
            }

            $file = File::find(ASSETS_DIR . $path);
            if ($file) {
                $deleted[] = $path;
                $file->delete();
            }
        }

        return $deleted;
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

            // Keep a copy of the old url to remplace with a new one in the html content
            $oldURL = $file->getURL();

            $name = pathinfo($file->Name, PATHINFO_FILENAME);
            $ext = pathinfo($file->Name, PATHINFO_EXTENSION);

            $file->Name = $name . '_' . time() . '.' . $ext;
            $file->ParentID = $folder->ID;
            $file->write();

            $replace[$oldURL] = $file->getURL();
        }

        $content = str_replace(array_keys($replace), array_values($replace), $content);

        return $content;
    }
}
