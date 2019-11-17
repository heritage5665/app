<?php
function setHead()
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type');
}

setHead();
define("APP_id", "7b585406");
define("APP_key", "b32fa9b848e9e2295b36a04ca68077de");
//define("GALLERY_name", "EdvesFaceidTest");
define("GALLERY_name", "EdvesDemoGallery");


set_time_limit(0);
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    //    print_r($url);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../src/App/App.php';


session_start();
require_once __DIR__ . '/../ForceUTF8/Encoding.php';


$db = new database();

$app->run();
