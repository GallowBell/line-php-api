<?php 

function CheckCaptionDuplicate($verify){
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
        'caption'
    ]);

    $captions = $_POST['caption'];

    foreach ($captions as $key => $value) {

        $caption = $value;
        $sql = "SELECT `caption`, `response_id` FROM `line_bot_caption` WHERE `line_bot_caption`.`caption` = ?";
        $data = $db_LINE->select($sql, [$caption])[0];
        $data['is_duplicate'] = (bool)!!count($data);
        $data['caption'] = $value;
        $result[] = $data;
    }

   
    return json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result
    ]);

}

?>