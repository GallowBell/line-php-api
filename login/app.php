<?php 
/* 

    APP FOR LINE LOGIN

*/

//autoload
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_ENV['APP_ENV'] == 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}else{
    error_reporting(0);
    ini_set('display_errors', '0');
}

//connection
require_once __DIR__ . '/../connection/connection.php';
$db_LINE = new Connection;
$db_LINE->connect();

//input JSON
$_JSON = json_decode(file_get_contents('php://input'), true);

//require function
require_once __DIR__ . '/../function.php';

//require jwt
require_once __DIR__ . '/jwt/index.php';

//require line-login
require_once __DIR__ . '/line-login/index.php';

$db_LINE->disconnect();

?>