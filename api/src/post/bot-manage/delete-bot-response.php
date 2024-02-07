<?php 

function deleteBotResponse($verify){
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

    $db_LINE->beginTransaction();

    $result_delete_response = $db_LINE->update("UPDATE `line_bot_response` SET 
        `is_hide` = 1,
        `active` = 0,
        `last_update` = NOW()
    WHERE `id` = ?;", 
    [
        $id
    ]);

    if(!$result_delete_response){
        $db_LINE->rollBack();
        echo json_encode([
            'status' => 500,
            'message' => 'Internal Server Error'
        ]);
        exit;
    }

    $result_delete_bot = $db_LINE->update("UPDATE `line_bot_caption` SET 
        `active` = ?,
        `last_update` = NOW()
    WHERE `response_id` = ?;", 
    [
        0,
        $id
    ]);

    echo json_encode([
        'status' => 200,
        'message' => 'OK'
    ]);
    exit;
}

?>