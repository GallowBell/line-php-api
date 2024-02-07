<?php 

require_once __DIR__ . '/Routes.php';

use XDark\Routes;

$Route = new Routes();

$Route->set_preURL('/m-webhook.xdark-protocol.com/class');

$Route->get('/index.php', function() {
    
    header('Content-type: application/json; charset=utf-8');
    
    return json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => [
            'version' => '1.0.0',
            'author' => 'xdark',
            'description' => 'LINE Message API'
        ],
        'server' => $_SERVER,
        'request_uri' => $_SERVER['REQUEST_URI']
    ]);
});

?>