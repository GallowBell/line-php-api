<?php 
function editUsers($verify){
    global $db_LINE;

    header('Content-type: application/json; charset=utf-8');
    $access_level = $verify->access_level;
    if($access_level < 100){
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    checkParameters([
        'id',
        'access_level',
        'is_active'
    ]);

    $id_line_user = $_POST['id'];
    $access_level = $_POST['access_level'];
    $is_active = $_POST['is_active'];

    $result = $db_LINE->update("UPDATE `line_user` SET 
        `access_level` = ?,
        `is_active` = ?
    WHERE `id` = ?;", 
    [
        $access_level,
        $is_active,
        $id_line_user
    ]);

    echo json_encode([
        'status' => 200,
        'message' => 'OK'
    ]);
    exit;
}
?>