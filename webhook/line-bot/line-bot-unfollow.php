<?php 


use \LINE\Clients\MessagingApi\Model\TextMessage;
use \LINE\Clients\MessagingApi\Model\FlexMessage;
use \LINE\Clients\MessagingApi\Model\Message;
use LINE\Constants\MessageType;

//basic push message

/* 
$userId = $_ENV['LINE_USER_ID_W'];
$text = $_JSON['events'][0]['message']['text'];
$message = new TextMessage(['type' => 'text', 'text' => 'hello! '.$text ]);
$response = pushMessage($userId, $message);  
*/

/* 
//basic reply message
$replyToken = $_JSON['events'][0]['replyToken'];
$text = $_JSON['events'][0]['message']['text'];
$message = new TextMessage(['type' => 'text', 'text' => 'ข้อความที่คุณพึ่งพิมพ์คือ '. $text]);
replyMessage($replyToken, $message);
 */

function LineBotUnFollow($events) {

    $replyToken = $events['replyToken'];
    $userId = $events['source']['userId'];
    $text = $events['message']['text'];
    $quoteToken = $events['message']['quoteToken'];
    $caption = getCaptionUnFollow();

    $rowCount = count($caption);

    if ($rowCount > 0) {

        for ($i=0; $i < $rowCount; $i++) { 

            $response_id = $caption[$i]['response_id'];
            $LINE_Response = getLINE_BOT_Response($response_id);

            if(count($LINE_Response) == 0){
                continue;
            }

            IncreaseResponseCount($LINE_Response[0]['id']);
            
            $is_use_ai = $LINE_Response[0]['is_use_ai'];
            $type = $LINE_Response[0]['type'];
            $content = $LINE_Response[0]['data_response'];
            $altText = $LINE_Response[0]['altText']? $LINE_Response[0]['altText'] : 'ข้อความตอบกลับอัตโนมัติ';

            if($is_use_ai == 1){

                require_once __DIR__ . '/../../api/src/post/AI/gpt-3.php';
                $GPT_result = GPTCompletions($text);
                $content = $GPT_result['choices'][0]['message']['content'];
                $type = 'text';

                if(empty($content)){
                    $content = $LINE_Response[0]['data_response'];
                }

            }

            if($type === 'text'){
                $message[] = new TextMessage([
                    'type' => MessageType::TEXT,
                    'text' => $content,
                    'quoteToken' => $quoteToken
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

        return true;
    }//end if check line_bot

    return false;

}


function getCaptionUnFollow(){
    global $db_LINE;
    $sql = "SELECT * FROM `line_bot_caption` WHERE `event_type` = ? AND `active` = ? LIMIT 5";
    $parameter = [4, 1];

    $result = $db_LINE->select($sql, $parameter);

    return $result;
}


?>

