<?php 
require_once __DIR__ . '/../../line-bot/line-bot-follow.php';

function initEventFollow($events, $webhook_id){

    save_webhook_follow($events, $webhook_id);

    $LineBotFollow = LineBotFollow($events, $webhook_id);

    save_log("init event follow LineBotFollow => ". $LineBotFollow);
}

function save_webhook_follow($events, $webhook_id) {
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

    $db_LINE->insert("INSERT INTO `line_event_follow_detail`(`webhook_id`, `userId`, `timestamp`, `mode`) VALUES (?, ?, ?, ?)", $parameter);

}

?>