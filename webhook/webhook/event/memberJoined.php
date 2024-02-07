<?php 
require_once __DIR__ . '/../../line-bot/line-bot-memberJoined.php';

function initEventmemberJoined($events, $webhook_id){

    save_webhook_memberJoined($events, $webhook_id);

    $LineBotFollow = LineBotmemberJoined($events, $webhook_id);

    save_log("init event follow LineBotmemberJoined => ". $LineBotFollow);
}

function save_webhook_memberJoined($events, $webhook_id) {
    global $db_LINE;
    
    $source = $events['source'];
    $userId = $source['userId'];
    $timestamp = $events['timestamp'];
    $mode = $events['mode'];

    $parameter = [
        $webhook_id,
        $userId,
        $timestamp,
        $mode
    ];

    //$db_LINE->insert("INSERT INTO `line_event_follow_detail`(`webhook_id`, `userId`, `timestamp`, `mode`) VALUES (?, ?, ?, ?)", $parameter);

}

?>