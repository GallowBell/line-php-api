<?php 
function activeBotResponse($verify) {
    global $db_LINE;

    header('Content-type: application/json; charset=utf-8');

    $access_level = $verify->access_level;

    if($access_level < 10){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }

    checkParameters(['id', 'active']);

    
    $id = $_POST['id'];
    $active = $_POST['active'];

    $isInRange = $active >= 0 && $active <= 1;

    if(!$isInRange){
        return json_encode([
            'status' => 400,
            'message' => 'Bad Request'
        ]);
    }

    $sql = "UPDATE `line_bot_response` SET 
                                        `active` = ?,
                                        `last_update` = NOW()
                                    WHERE `line_bot_response`.`id` = ?";
    $result = $db_LINE->update($sql, [$active, $id]);

    if(!$result) {
        return json_encode([
            'status' => 500,
            'message' => 'Something went wrong please try again'
        ]);
    } 

    return json_encode([
        'status' => 200,
        'message' => 'OK'
    ]);
}
?>