<?php 

//autoload
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

/**
 *  * FULL URL OF THE APP
 * @var string $APP_URL
 */
$APP_URL = $_ENV['APP_URL'];

/**
 * PRE_REQUEST_URI
 * @var string $pre_URL
 */
$pre_URL = '/admin';

/**
 * REQUEST_URI
 * @var string $req_URL
 */
$req_URL = explode($pre_URL, explode('?', $_SERVER['REQUEST_URI'])[0])[1];


if($_ENV['APP_DEBUG'] == 'true') {
    require_once __DIR__ . '/under_maintenance.php';
    exit;
}

if ($_ENV['APP_ENV'] == 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}else{
    error_reporting(0);
    ini_set('display_errors', '0');
}

require_once __DIR__ . '/../connection/connection.php';

$db_LINE = new Connection;
$db_LINE->connect();

require_once __DIR__ ."/../login/jwt/index.php";

require_once __DIR__ . '/../function.php';

?>