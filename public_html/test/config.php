<?php 

//autoload
require_once __DIR__ . '/../../vendor/autoload.php';

//require_once __DIR__ . '/../../connection/connection.php';
require_once __DIR__ . '/../../connection/connection.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db_Temp = new Connection([
    'DB_HOST' => $_ENV['DB_HOST'],
    'DB_USERNAME' => $_ENV['DB_USERNAME'],
    'DB_PASSWORD' => $_ENV['DB_PASSWORD'],
    'DB_NAME' => $_ENV['DB_NAME'],
    'DB_CHARSET' => $_ENV['DB_CHARSET'],
    'DEBUG_MODE' => $_ENV['DEBUG_MODE']
]);
$db_Temp->connect();

$TOKEN_LIST = [
    //NVK - นวนคร
    "bG50rNm9DSXtvscUeI3YS6IbzawOThfzfP4pv6YHWaj"
];


?>