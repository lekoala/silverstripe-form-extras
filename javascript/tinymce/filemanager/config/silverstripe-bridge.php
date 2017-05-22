<?php
/**
 * This file is needed to initialize SilverStripe core and define some custom feature
 */
define('FILEMANAGER_RELATIVE_PATH', '../../../../');

// Load in core
require_once(FILEMANAGER_RELATIVE_PATH . 'framework/core/Core.php');

// Connect to database
require_once(FILEMANAGER_RELATIVE_PATH . 'framework/model/DB.php');
global $databaseConfig;
if ($databaseConfig)
    DB::connect($databaseConfig);

Session::start();

if (!Member::currentUserID()) {
    die();
}

$_ssFolder = null;



$_ssFolder = Folder::find_or_make('userfiles/' . Member::currentUserID());

// Ends with slash!
$_uploadDir = 'assets/userfiles/' . Member::currentUserID() . '/';
$_currentPath = FILEMANAGER_RELATIVE_PATH . $_uploadDir . '/';
$_thumbsPath = FILEMANAGER_RELATIVE_PATH . 'thumbs/' . Member::currentUserID() . '/';

// Init thumbs
if (!is_dir($_thumbsPath)) {
    mkdir($_thumbsPath, 0777, true);
    file_put_contents(dirname($_thumbsPath) . '/_manifest_exclude', '');
}

// Keep assets db in sync with filesystem
register_shutdown_function(function() use ($_ssFolder) {
    if (!$_ssFolder) {
        return;
    }
    $_ssFolder->syncChildren();
});

//function response($content = '', $statusCode = 200, $headers = array())
//{
//    $response = new SS_HTTPResponse($content, $statusCode);
//    foreach($headers as $header => $value) {
//         $response->addHeader($header, $value);
//    }
//    return $response
//}
function get_silverstripe_language()
{
    $locale = i18n::get_locale();
    if (class_exists('Fluent')) {
        $locale = Fluent::get_persist_locale();
    }
    $lang = i18n::get_lang_from_locale($locale);
    if ($lang == 'en') {
        return 'en_EN';
    }
    if ($lang == 'fr') {
        return 'fr_FR';
    }
    // Otherwise look in lang folder
    $ulocale = str_replace('-', '_', $locale);
    $lang_folder = dirname(__DIR__) . '/lang/';
    if (is_file($lang_folder . $lang . '.php')) {
        return $lang;
    }
    if (is_file($lang_folder . $ulocale . '.php')) {
        return $ulocale;
    }
    return 'en_EN';
}

function get_silverstripe_max_upload()
{
    $maxUpload = File::ini2bytes(ini_get('upload_max_filesize'));
    $maxPost = File::ini2bytes(ini_get('post_max_size'));
    $v = min($maxUpload, $maxPost);
    $mb = round($v / 1048576);

    if ($mb > 1) {
        return $mb;
    }
    return 1;
}
