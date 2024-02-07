<?php 

require_once __DIR__ . '/middleware.php';


$HEADER_SAVE_ID = save_header();

//if method not == post
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    echo json_encode(array(
        'status' => 405,
        'message' => 'method not allowed'
    ));
    $db_LINE->disconnect();
    exit();
}

//check signature line
if(!verify_signature(array(
    'body' => file_get_contents('php://input'),
    'header' => getHeader()
))){
    echo json_encode(array(
        'status' => 401,
        'message' => 'signature not match'
    ));
    $db_LINE->disconnect();
    exit();
}

?>