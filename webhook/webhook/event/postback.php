<?php 

//require line-bot-message
require_once __DIR__ . '/../../line-bot/line-bot-postback.php';

function initEventPostBack($events, $webhook_id){

    save_webhook_postback($events, $webhook_id);
    
    $lineBotMessage = LineBotPostBack($events, $webhook_id);

    $parsePostback = parsePostback($events);
    
    savePostbackParameter($parsePostback, $webhook_id, $lineBotMessage);

    save_log("init event postback lineBotPostBack => ". $lineBotMessage);

}

function save_webhook_postback($events, $webhook_id) {
    global $db_LINE;
    
    $postback = $events['postback'];
    $data = $postback['data'];

    $db_LINE->insert("INSERT INTO `line_event_postback_detail`( `webhook_id`, `data` ) VALUES (?, ?)", [ $webhook_id, $data]);

}

function savePostbackParameter($parsePostback, $webhook_id, $response_id = null) {
    global $db_LINE;
    
    foreach ($parsePostback as $key => $value) {
        $db_LINE->insert("INSERT INTO `line_postback_parameter` (`parameter`, `value`, `webhook_id`, `response_id` ) VALUES (?, ?, ?, ? );", [$key, $value, $webhook_id, $response_id]);
    }

}
?>