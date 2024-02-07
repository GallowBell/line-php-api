<?php 
require_once __DIR__ . '/../../line-bot/line-bot-unfollow.php';
function initEventUnFollow($events, $webhook_id){

    save_webhook_unfollow($events, $webhook_id);

    $LineBotunFollow = LineBotUnFollow($events);

    save_log("init event unfollow LineBotunFollow => ". $LineBotunFollow);
}

function save_webhook_unfollow($events, $webhook_id) {
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

    $db_LINE->insert("INSERT INTO `line_event_unfollow_detail`(`webhook_id`, `userId`, `timestamp`, `mode`) VALUES (?, ?, ?, ?)", $parameter);

}

?>