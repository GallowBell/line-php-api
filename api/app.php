<?php 

/* 

    APP FOR LINE MESSAGE API

*/

require_once __DIR__ . '/config.php';

//require middleware
require_once __DIR__ . '/middleware.php';

//require get
require_once __DIR__ . '/GET.php';

//require post
require_once __DIR__ . '/POST.php';



http_response_code(400);
echo json_encode([
    'status' => 400,
    'message'=> 'Bad Request'
]);

?>