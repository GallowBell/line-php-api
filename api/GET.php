<?php

if ($req_METHOD !== 'GET') {

    return;
}

$Routes->get('/server-log', function () {

    header('Content-type: application/json; charset=utf-8');
    return json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => [
            'version' => '1.0.0',
            'author' => 'xdark',
            'description' => 'LINE Message API'
        ],
    ]);
});



?>