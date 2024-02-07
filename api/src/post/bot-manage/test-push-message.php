<?php 

function test_push_message(){
    global $APP_URL, $API_PUSH_MESSAGE, $db_LINE, $verify;
    
    header('Content-type: application/json; charset=utf-8');

    $access_level = $verify->access_level;

    if($access_level < 10){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }

    if(isset($_POST['id']) && !empty($_POST['id'])){
        checkParameters(['id']);
        $id = $_POST['id'];
        $line_response = $db_LINE->select(" SELECT * FROM `line_bot_response` WHERE id = ?", [$id])[0];
    }else{
        checkParameters(['data_response', 'type']);
        $data_response = $_POST['data_response'];
        $type = $_POST['type'];
        $line_response = [
            'data_response' => $data_response,
            'type' => $type
        ];
    }

    $API_URL_PUSH = $APP_URL . $_ENV['API_PRE_URL'].$API_PUSH_MESSAGE;
    $userId = selectUserData($verify->id_line_user)[0]['userId'];

    if(!$userId){
        return json_encode([
            'status' => 500,
            'message' => 'ไม่พบผู้ใช้งานในการส่ง Message API'
        ]);
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $API_URL_PUSH,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'to' => $userId,
            'messages' => $line_response['data_response'],
            'type' => $line_response['type'],
            'altText' => 'ทดสอบ'
        ),
        CURLOPT_HTTPHEADER => array(
            'Cookie: token='.$_COOKIE['token']
        ),
    ));

    $response = curl_exec($curl);

    $error = curl_errno($curl);

    //curl get error
    if($error){
        return json_encode([
            'status' => 500,
            'message' => $error
        ]);
    }

    $result = json_decode($response, true);

    curl_close($curl);

    return json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result
    ]);

}

?>