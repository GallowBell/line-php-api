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

function LineBotMessage($events, $webhook_id) {
    global $dbMessageType, $LINE_USER;

    $replyToken = $events['replyToken'];
    $userId = $events['source']['userId'];
    $text = $events['message']['text'];
    $quoteToken = $events['message']['quoteToken'];
    $dbMessageType = getDBMessageType($events);
    $caption = getCaption($text, $dbMessageType[0]['id']);
    $message_id = $events['message']['id'];

    save_log("LINE_USER => ". json_encode($LINE_USER));

    $displayName = $LINE_USER['displayName'];

    $rowCount = count($caption);
    if ($rowCount > 0) {

        for ($i=0; $i < $rowCount; $i++) { 

            if($caption[$i]['is_regex'] == 0){
                if($caption[$i]['caption'] !== $text){
                    continue;
                }
            }

            $response_id = $caption[$i]['response_id'];
            $caption_id = $caption[$i]['id'];
            $LINE_Response = getLINE_BOT_Response($response_id);

            if(count($LINE_Response) == 0){
                continue;
            }

            save_log('$webhook_id =>'. $webhook_id);
            $parameter_saveCount = [
                'caption_id' => $caption_id,
                'webhook_id' => $webhook_id
            ];

            IncreaseResponseCount($LINE_Response[0]['id'], $parameter_saveCount);

            $is_use_ai = $LINE_Response[0]['is_use_ai'];
            $type = $LINE_Response[0]['type'];
            $content = $LINE_Response[0]['data_response'];
            $altText = $LINE_Response[0]['altText']? $LINE_Response[0]['altText'] : 'ข้อความตอบกลับอัตโนมัติ';

            if($is_use_ai == 1){

                require_once __DIR__ . '/../../api/src/post/AI/gpt-3.php';
                $GPT_result = GPTCompletions($text, ['message_id' => $message_id]);
                $content = $GPT_result['choices'][0]['message']['content'];
                $type = 'text';

                if(empty($content)){
                    $content = $LINE_Response[0]['data_response'];
                }

            }

            $content = str_replace($_ENV['LINE_DISPLAY_NAME'], $displayName, $content);

            if($type === 'text') {
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




//function generate line flex message
/* function generateLINE_Flex_Message($flex){
    $flex = json_encode($flex);
    $flex = json_decode($flex);
    $flex = new FlexMessage($flex);
    return $flex;
} */



?>