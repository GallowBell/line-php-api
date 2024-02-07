<?php 

require_once __DIR__ . '/../../login/app.php';


header('Content-type: application/json; charset=utf-8');
http_response_code(400);
echo json_encode([
    'status' => 400,
    'message' => 'Bad Request'
]);

?>