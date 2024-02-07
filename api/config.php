<?php 


header('Content-type: application/json; charset=utf-8');

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$APP_URL = $_ENV['APP_URL'];

/* if ($_ENV['APP_ENV'] == 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}else{
    error_reporting(0);
    ini_set('display_errors', '0');
} */

//connection
require_once __DIR__ . '/../connection/connection.php';
$db_LINE = new Connection;
$db_LINE->connect();
$db_LINE->setRedisCacheTime(0);

//input JSON
$_JSON = json_decode(file_get_contents('php://input'), true);

//require jwt
require_once __DIR__ . '/../login/jwt/index.php';

//require function
require_once __DIR__ . '/../function.php';

//require line message api
require_once __DIR__ . '/../webhook/config.php';

/**
 * Class Routes
 */
require_once __DIR__ . '/../class/Routes.php';
use XDark\Routes;

/**
 * PRE_REQUEST_URI
 * @var string $pre_URL
 */
$pre_URL = $_ENV['API_PRE_URL'];

/**
 * REQUEST_URI
 * @var string $req_URL
 */
$req_URL = explode($pre_URL, explode('?', $_SERVER['REQUEST_URI'])[0])[1];

/**
 * REQUEST_METHOD
 * @var string $req_METHOD
 */
$req_METHOD = $_SERVER['REQUEST_METHOD'];


/**
 * Routes define
 * @var Routes $Routes
 */
$Routes = new Routes();

$Routes->set_preURL($pre_URL);

?>