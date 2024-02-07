<?php 
function deleteUser($verify) {
    global $db_LINE;

    header('Content-type: application/json; charset=utf-8');
    $access_level = $verify->access_level;
    if($access_level < 100){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }

    checkParameters([
        'id'
    ]);

    $id_line_user = $_POST['id'];
    $result = $db_LINE->update("UPDATE `line_user` SET 
        `is_hide` = 1,
        `is_active` = 0
    WHERE `id` = ?;", 
    [
        $id_line_user
    ]);

    if(!$result){
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