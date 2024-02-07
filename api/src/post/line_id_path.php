<?php 

function line_id_path(){
    
    header('Content-type: application/json; charset=utf-8');

    if(!isset($_SERVER['HTTP_X_API_KEY'])){
        http_response_code(401);
        return json_encode([
            'status' => 401,
            'message' => 'Unauthorized'
        ]);
    }

    $jwt = $_SERVER['HTTP_X_API_KEY'];
    $API_MMT_JWT_KEY = $_ENV['API_MMT_JWT_KEY'];
    $verify = checkJWT($jwt, $API_MMT_JWT_KEY);

    if(!$verify){
        http_response_code(401);
        return json_encode([
            'status' => 401,
            'message' => 'Unauthorized',
        ]);
    }

    if(!isset($_POST['cid'])){
        http_response_code(400);
        return json_encode([
            'status' => 400,
            'message' => 'cid is required'
        ]);
    }

    $cid = $_POST['cid'];
    $UserData = selectUserDataByCID($cid);

    if(count($UserData) <= 0) {
        return json_encode([
            'status' => 404,
            'message' => 'Not Found',
            'data' => []
        ]);
    }

    return json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $UserData
    ]);
}

?>