<?php 



header('Content-type: application/json; charset=utf-8');

// your code here
$_JSON = json_decode(file_get_contents('php://input'), true);

//echo file_exists(__DIR__ . '/../../src/app.php');

require_once __DIR__ . '/../../webhook/app.php';

echo json_encode([
    'status' => 200,
    'message' => 'OK'
]);

?>