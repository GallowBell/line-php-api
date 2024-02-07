<?php 

use \LINE\Clients\MessagingApi\Model\TextMessage;
use \LINE\Clients\MessagingApi\Model\FlexMessage;
use LINE\Constants\MessageType;

/**
 * LINE Messaging API PUSH MESSAGE to send message to user directly
 */
function api_push_message(){
    
    header('Content-type: application/json; charset=utf-8');

    //check isset
    if(!isset($_POST['to']) || !isset($_POST['messages']) || !isset($_POST['type'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 400,
            'message' => 'parameter to, messages, type is required'
        ]);
        exit ;
    }

    //check empty
    if(empty($_POST['to']) || empty($_POST['messages']) || empty($_POST['type'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 400,
            'message' => "parameter to, messages, type is can't be empty"
        ]);
        exit ;
    }

    $to = $_POST['to'];
    $type = $_POST['type'];

    require_once __DIR__ . '/../../../webhook/config.php';

    if($type === 'text'){
        $message = new TextMessage([
            'type' => MessageType::TEXT,
            'text' => $_POST['messages']
        ]);
    }

    if($type === 'flex') { 
        //convert json to array
        $messages = json_decode($_POST['messages'], true);        
        $altText = $_POST['altText']?$_POST['altText']:'ข้อความอัตโนมัติ';
        
        $message = new FlexMessage([
            'type' => MessageType::FLEX,
            'altText' => $altText,
            'contents' => $messages
        ]);
    }

    $result = pushMessage($to, $message);

    if(!isset($result['sentMessages'])){
        http_response_code(400);
        echo json_encode([
            'status' => 400,
            'message' => 'Bad Request',
            'result' => $result
        ]);
        exit;
    }

    echo json_encode([
        'status' => 200,
        'message' => 'OK',
        'result' => $result
    ]);
    exit;
}

?>