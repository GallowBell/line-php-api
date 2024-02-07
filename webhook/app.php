<?php 

//autoload
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../') ;
$dotenv->load();

if ($_ENV['APP_ENV'] == 'development') {
    error_reporting(E_ALL);
}else{
    error_reporting(0);
}

//connection
require_once __DIR__ . '/../connection/connection.php';
$db_LINE = new Connection;
$db_LINE->connect();

//input JSON
$_JSON = json_decode(file_get_contents('php://input'), true);

//require function
require_once __DIR__ . '/../function.php';

//jwt
require_once __DIR__ . '/jwt/jwt.php';

//middleware
require_once __DIR__ . '/middleware/index.php';

//require line config
require_once __DIR__ . '/config.php';

//require line-bot-message
//require_once __DIR__ . '/line-bot/line-bot-message.php';

//require webhook
require_once __DIR__ . '/webhook/index.php';

$db_LINE->disconnect();


?>