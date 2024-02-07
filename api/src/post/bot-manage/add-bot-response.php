<?php 

function addBotResponse($verify){
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
        'caption',
        'type',
        'data_response',
        'response_title',
        'event_type'
    ]);

    if (isset($_POST['is_use_time']))  {
        checkParameters([
            'response_time_start',
            'response_time_end'
        ]);
    }

    if ($_POST['type'] == 'flex')  {
        checkParameters([
            'altText'
        ]);
    }

    $altText = $_POST['altText']?$_POST['altText']:null;
    $captions = $_POST['caption'];
    $type = $_POST['type'];
    $data_response = $_POST['data_response'];
    $is_use_ai = $_POST['is_use_ai']?$_POST['is_use_ai']:0;

    if($type == 'text'){
        $data_response = str_replace("
        ", '\n', $_POST['data_response']);
    }else{
        checkParameters([
            'altText'
        ]);
    }

    
    $response_title = $_POST['response_title'];
    $response_time_start = $_POST['response_time_start']?$_POST['response_time_start']:null;
    $response_time_end = $_POST['response_time_end']?$_POST['response_time_end']:null;
    $is_use_time = $_POST['is_use_time']?$_POST['is_use_time']:0;
    $is_regex = $_POST['is_regex']?$_POST['is_regex']:0;
    $notificationDisabled = (bool)$_POST['notificationDisabled']?$_POST['notificationDisabled']:0;
    $event_type = $_POST['event_type'];

    $total_captions = count($captions);

    if($total_captions == 0) {
        return json_encode([
            'status' => 400,
            'message' => 'Bad Request'
        ]);
    }

    $sql = "INSERT INTO `line_bot_response` (
                                                `title`,
                                                `data_response`,
                                                `altText`,
                                                `active`,
                                                `notificationDisabled`,
                                                `type`,
                                                `is_use_time`,
                                                `is_use_ai`,
                                                `start_time`,
                                                `end_time`
                                            ) VALUES (
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?
                                            );`";

    //begin transaction
    $db_LINE->beginTransaction();

    $id = $db_LINE->insert($sql, [
        $response_title,
        $data_response,
        $altText,
        1,
        $notificationDisabled,
        $type,
        $is_use_time,
        $is_use_ai,
        $response_time_start,
        $response_time_end
    ]);

    if (!$id) {
        $db_LINE->rollBack();
        return json_encode([
            'status' => 500,
            'message' => 'Something went wrong please try again',
            'error' => 'update'
        ]);
    }

    for ($i=0; $i < $total_captions; $i++) { 
        $caption = $captions[$i];
        $sql = "INSERT INTO `line_bot_caption` (
                                                `caption`,
                                                `response_id`,
                                                `active`,
                                                `event_type`,
                                                `is_regex`
                                            ) VALUES (
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?
                                            );";
        $result = $db_LINE->insert($sql, [$caption, $id, 1, $event_type, $is_regex]);

        if(!$result){
            $db_LINE->rollBack();
            return json_encode([
                'status' => 500,
                'message' => 'Something went wrong please try again',
                'error' => 'insert'
            ]);
        
        }
    }

    $result_commit = $db_LINE->commit();

    if(!$result_commit){
        $db_LINE->rollBack();
        return json_encode([
            'status' => 500,
            'message' => 'Something went wrong please try again',
            'error' => 'commit'
        ]);
    }

    return json_encode([
        'status' => 200,
        'message' => 'OK'
    ]);


}

?>