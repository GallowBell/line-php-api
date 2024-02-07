<?php 
function get_users($verify){
    global $db_LINE;

    header('Content-type: application/json; charset=utf-8');
    $access_level = $verify->access_level;
    if($access_level < 100){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }

    $result = $db_LINE->select("SELECT * FROM `line_user` WHERE access_level > 1 AND is_hide = 0 Order BY id ASC;");

    return json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result,
    ]);

}
?>