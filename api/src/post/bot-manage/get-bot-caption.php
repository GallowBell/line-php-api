<?php 

function getBotCaptionByResponseID($verify){
    global $db_LINE;

    header('Content-type: application/json; charset=utf-8');
    $access_level = $verify->access_level;
    if($access_level < 10){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }

    checkParameters([
        'id'
    ]);

    $id = $_POST['id'];

    $result = $db_LINE->select("SELECT * FROM `line_bot_caption` WHERE `response_id` = ?;", [$id]);

    if(!$result) {
        return json_encode([
            'status' => 500,
            'message' => 'Something went wrong please try again'
        ]);
    } 

    return json_encode($result);
}

?>