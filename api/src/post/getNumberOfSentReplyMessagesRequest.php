<?php 

/**
 * @param $_POST['date'] string yyyyMMdd
 */
function APIgetNumberOfSentReplyMessagesRequest($verify){
    global $db_LINE;

    header('Content-type: application/json; charset=utf-8');
    $access_level = $verify->access_level;
    if($access_level < 10){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }

    if(!isset($_POST['date'])){
        return json_encode([
            'status' => 400,
            'message' => 'Bad Request'
        ]);
    }

    if(empty($_POST['date'])){
        return json_encode([
            'status' => 400,
            'message' => "parameter date is can't be empty"
        ]);
    }

    $date = $_POST['date'];

    require_once __DIR__ . '/../../../webhook/config.php';

    $result = getNumberOfSentReplyMessagesRequest($date);

    return json_encode($result);

}

?>