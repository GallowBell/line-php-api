<?php 

function CheckLogin($verify){
    global $db_LINE;

    header('Content-type: application/json; charset=utf-8');

    $access_level = $verify->access_level;

    if($access_level < 100){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }

    checkParameters(['id']);

    $id = $_POST['id'];
    $result = $db_LINE->select("SELECT * FROM `line_user` WHERE access_level > 1 AND id = ? Order BY id ASC;", [$id]);

    $total = count($result);
    if($total == 0){
        return json_encode([
            'status' => 404,
            'message' => 'Not Found',
            'result' => true
        ]);
    }

    $data = $result[0];

    if(empty($data['userId'])){
        return json_encode([
            'status' => 200,
            'message' => 'OK',
            'result' => false
        ]);
    }

    return json_encode([
        'status' => 200,
        'message' => 'OK',
        'result' => true
    ]);

}

?>