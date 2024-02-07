<?php 

use \LINE\Clients\MessagingApi\Model\TextMessage;
use \LINE\Clients\MessagingApi\Model\FlexMessage;
use LINE\Constants\MessageType;

function LineBotPostBack($events, $webhook_id) {

    $source = $events['source'];
    $userId = $source['userId'];
    $replyToken = $events['replyToken'];
    $parsePostback = parsePostback($events);

    save_log(json_encode($parsePostback));

    if(!isset($parsePostback['action'])) {
        return false;
    }

    $action = $parsePostback['action']?$parsePostback['action']:"";
    $caption = getCaption($action, '9');
    $rowCount = count($caption);

    if ($rowCount > 0) {

        for ($i=0; $i < $rowCount; $i++) { 

            if($caption[$i]['is_regex'] == 0){
                if($caption[$i]['caption'] !== $action){
                    continue;
                }
            }

            $response_id = $caption[$i]['response_id'];
            $LINE_Response = getLINE_BOT_Response($response_id);
            $caption_id = $caption[$i]['id'];

            $parameter_saveCount = [
                'caption_id' => $caption_id,
                'webhook_id' => $webhook_id
            ];

            IncreaseResponseCount($LINE_Response[0]['id'], $parameter_saveCount);
            
            $type = $LINE_Response[0]['type'];
            $content = $LINE_Response[0]['data_response'];
            $altText = $LINE_Response[0]['altText']? $LINE_Response[0]['altText'] : 'ข้อความตอบกลับอัตโนมัติ';

            if($type === 'text'){
                $message[] = new TextMessage([
                    'type' => MessageType::TEXT,
                    'text' => $content
                ]);
            }

            if($type === 'flex') {
                //convert json to array
                $contents = json_decode($content, true);
                $message[] = new FlexMessage([
                    'type' => MessageType::FLEX,
                    'altText' => $altText,
                    'contents' => $contents
                ]);
            }
        }
        
        $response = replyMessage($replyToken, $message);
        return $LINE_Response[0]['id'];
    }//end if check line_bot

    //action check_credit
    if($action == 'check_credit') {
        require_once __DIR__ . '/src/postback/check_credit.php';
        $message = CheckCredit(['userId' => $userId]);
        $response = replyMessage($replyToken, $message);
        save_log("LineBotPostBack check_credit => ". json_encode($response));
        return true;
    }

    return false;
}

/**
 * parse postback data
 * @param array $events
 * @return array
 */
function parsePostback($events) {

    $postback = $events['postback'];
    $data = $postback['data'];

    $data = explode('&', $data);

    $data = array_map(function($item) {
        $item = explode('=', $item);
        return $item;
    }, $data);

    $data = array_combine(array_column($data, 0), array_column($data, 1));

    return $data;
}




?>