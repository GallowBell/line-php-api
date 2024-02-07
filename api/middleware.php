<?php


if (!isset($_SERVER['HTTP_X_API_KEY'])) {

    if (!isset($_COOKIE['token'])) {
        http_response_code(401);
        echo json_encode([
            'status' => 401,
            'message' => 'Unauthorized'
        ]);
        exit;

    } 

    //check jwt for line
    $jwt = $_COOKIE['token'];
    $verify = checkJWT($jwt);

} else {

    //check jwt for mmt
    $jwt = $_SERVER['HTTP_X_API_KEY'];
    $API_MMT_JWT_KEY = $_ENV['API_MMT_JWT_KEY'];
    $verify = checkJWT($jwt, $API_MMT_JWT_KEY);
}


if (!$verify) {

    http_response_code(401);
    echo json_encode([
        'status' => 401,
        'message' => 'Unauthorized',

    ]);
    exit;

}

$access_level = $verify->access_level ? $verify->access_level : 0;

// Get the current hour
$currentHour = date('Y-m-d H');
$API_LIMIT_REQUEST = $_ENV['API_LIMIT_REQUEST'];

if (!isset($_SESSION['request_count']) || $_SESSION['request_hour'] != $currentHour) {
    // If the session variable is not set or the hour has changed, reset the count
    $_SESSION['request_count'] = 1;
    $_SESSION['request_hour'] = $currentHour;
} else {
    // Otherwise, increment the count
    $_SESSION['request_count']++;
}

// Check if the limit has been reached
if ($_SESSION['request_count'] > $API_LIMIT_REQUEST) {
    // If the limit has been reached, send an error response
    http_response_code(429);
    echo json_encode([
        'status' => 429,
        'message' => 'Rate limit exceeded. Try again later.',
    ]);
    exit;
}


?>