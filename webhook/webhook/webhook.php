<?php 

use \LINE\Clients\MessagingApi\Model\UserProfileResponse;

global $events, $LINE_USER;

//function get total event
function getTotalEvent(){
    global $_JSON;
    $total_event = count($_JSON['events']);
    return $total_event;
}

//function getTypeEvent
function getCommonProperties($index){
    global $_JSON, $events;
    $events = $_JSON['events'][$index];
    return $events;
}

//function check event type
function handleEventType($events, $webhook_id){

    $type = $events['type'];

    //handle message event
    if($type == 'message') {    

        require_once __DIR__ . '/event/message.php'; 

        initEventMessage($events, $webhook_id);  
        
        save_log("after initEventMessage ");

        return ;
    }

    if($type == 'follow') {

        save_log("init event follow => ". json_encode($events));

        require_once __DIR__ . '/event/follow.php'; 

        initEventFollow($events, $webhook_id);  
        
        save_log("after init event follow ");

        return ;
    }

    if($type == 'unfollow') {

        save_log("init event follow => ". json_encode($events));

        require_once __DIR__ . '/event/unfollow.php'; 

        initEventUnFollow($events, $webhook_id);  
        
        save_log("after init event follow ");

        return ;
    }

    if($type == 'postback') {

        save_log("init event postback => ". json_encode($events));

        require_once __DIR__ . '/event/postback.php'; 

        initEventPostBack($events, $webhook_id);  
        
        save_log("after init event postback ");

        return ;
    }

    if($type == 'memberJoined') {

        save_log("init event memberJoined => ". json_encode($events));

        require_once __DIR__ . '/event/memberJoined.php'; 

        initEventmemberJoined($events, $webhook_id);  
        
        save_log("after init event memberJoined ");

        return ;
    }

}

//save log webhook
function save_webhook($events, $destination){
    global $db_LINE, $HEADER_SAVE_ID;

    $event_type = $db_LINE->select("SELECT * FROM `line_event_type` WHERE `event_type` = ?", [$events['type']]);

    save_log('$event_type => ' . json_encode($event_type));
    save_log("event_type[0]['id'] => ". $event_type[0]['id']);

    $replyToken = $events['replyToken'];
    
    $source = $events['source'];
    $userId = $source['userId'];
    $sourceType = $source['type'];

    if($sourceType == 'group'){
        $groupId = $source['groupId'];
    }

    if($sourceType == 'room'){
        $roomId = $source['roomId'];
    }

    $timestamp = $events['timestamp'];
    $webhookEventId = $events['webhookEventId'];
    $replyToken = $events['replyToken'];
    $type = $event_type[0]['id'];
    $header = getallheaders(); //getHeader('x-line-signature');

    $events_encode = json_encode($events);

    $line_signature = $header['X-Line-Signature'];
    $sql = " INSERT INTO `line_web_hook` (
        `response`,
        `destination`,
        `event`,
        `header`,
        `line_signature`,
        `replyToken`,
        `userId`,
        `groupId`,
        `roomId`,
        `sourceType`,
        `timestamp`,
        `webhookEventId`,
        `type`
    ) VALUES (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )";

    $parameter = [
        json_encode($events),
        $destination,
        $events_encode,
        $HEADER_SAVE_ID,
        $line_signature,
        $replyToken,
        $userId,
        $groupId,
        $roomId,
        $sourceType,
        $timestamp,
        $webhookEventId,
        $type
    ];
    
    $profile = getProfile($userId);

    save_log(" profile => " .  $profile->getDisplayName() );
    //getProfileFromLINE($userId);

    $result = $db_LINE->insert($sql, $parameter);

    save_log('save webhook => '.json_encode($result));

    return $result;

}

//function get profile from db
function getProfile($userId){
    global $db_LINE, $LINE_USER;

    $result = $db_LINE->select("SELECT * FROM `line_user` WHERE `userId` = ?", [$userId]);
    $profile = $result[0];

    $displayName = $profile['displayName']?$profile['displayName']:"";
    $pictureUrl = $profile['pictureUrl']?$profile['pictureUrl']:"";
    $statusMessage = $profile['statusMessage']?$profile['statusMessage']:"";
    $language = $profile['language']?$profile['language']:"";

    $total_found_user = count($result);

    save_log("logic getProfile => ". $total_found_user == 0 || empty($statusMessage) || empty($language));
   
    save_log("profile getProfile => ". json_encode([
        'pictureUrl' => $pictureUrl,
        'statusMessage' => $statusMessage,
        'language' => $language,
        'total' => $total_found_user
    ]));

    if($total_found_user == 0 || empty($statusMessage) || empty($language)){

        $LINE_USER = getProfileFromLINE($userId);

        if( (!$statusMessage || !$language) && $total_found_user > 0){

            $displayName = $LINE_USER->getdisplayName();
            $pictureUrl = $LINE_USER->getpictureUrl();
            $statusMessage = $LINE_USER->getstatusMessage();
            $language = $LINE_USER->getlanguage();
            $userId = $LINE_USER->getuserId();

            $sql = "UPDATE `line_user` (
                SET `displayName` = ?,
                SET `pictureUrl` = ?,
                SET `statusMessage` = ?,
                SET `language` = ?
            WHERE `userId` = ? ;";

            $parameter = [
                $displayName,
                $pictureUrl,
                $statusMessage,
                $language,
                $userId
            ];

            $res_update = $db_LINE->update("UPDATE `line_user`  SET 
                `displayName` = ?,
                `pictureUrl` = ?,
                `statusMessage` = ?,
                `language` = ?
            WHERE `line_user`.`userId` = ? ", [
                $displayName,
                $pictureUrl,
                $statusMessage,
                $language,
                $userId
            ]);

            save_log("profile getProfile => ". json_encode([
                'pictureUrl' => $pictureUrl,
                'statusMessage' => $statusMessage,
                'language' => $language,
                'update_res' => $res_update
            ]));

            return $LINE_USER;
        }

        $displayName = $LINE_USER->getdisplayName();
        $pictureUrl = $LINE_USER->getpictureUrl();
        $statusMessage = $LINE_USER->getstatusMessage()?$LINE_USER->getstatusMessage():"";
        $language = $LINE_USER->getlanguage()?$LINE_USER->getlanguage():"";
        $userId = $LINE_USER->getuserId();

        

        
        $sql = "INSERT INTO `line_user` (
            `displayName`,
            `pictureUrl`,
            `statusMessage`,
            `language`,
            `userId`
        ) VALUES (
            ?,
            ?,
            ?,
            ?,
            ?
        )";
        
        $parameter = [
            $displayName,
            $pictureUrl,
            $statusMessage,
            $language,
            $userId
        ];
        $db_LINE->insert($sql, $parameter);

    }else{
        $LINE_USER = new UserProfileResponse($profile);
    }

    return $LINE_USER;
}


/**
 * Get profile from LINE API
 * @param string $userId
 * @return UserProfileResponse
 */
function getProfileFromLINE($userId){
    global $messagingApi, $LINE_USER;

    //$channelAccessToken = $_ENV['LINE_BOT_CHANNEL_ACCESS_TOKEN'];
    $getProfile = $messagingApi->getProfile($userId);

    //convert to array
    $result = json_decode($getProfile, true);

    //store data to object
    $LINE_USER = new UserProfileResponse($result);

    return $LINE_USER;

}

function initWebhook(){
    global $_JSON;

    $total = getTotalEvent();
    $destination = $_JSON["destination"];
    
    /* $botinfo = getBotInfo();

    save_log("initWebhook botinfo => ". json_encode($botinfo)); */
    //$botinfo

    
    for($i = 0; $i < $total; $i++) {

        $events = getCommonProperties($i);
        $isRedelivery = $events['deliveryContext']['isRedelivery'];
        $mode = $events['mode'];

        if($isRedelivery){
            $webhookEventId = $events['webhookEventId'];
            $webhook_data = getWebhookBywebhookEventId($webhookEventId);
            if(count($webhook_data) > 0){
                continue;
            }
        }


        $webhook_id = save_webhook($events, $destination);
        /* if($botinfo['data']['chatMode'] == "chat"){
            continue;
        } */

        handleEventType($events, $webhook_id);
    }

}

function getWebhookBywebhookEventId($webhookEventId){
    global $db_LINE;
    $result = $db_LINE->select("SELECT * FROM `line_web_hook` WHERE `webhookEventId` = ?", [$webhookEventId]);
    return $result;
}

function getEventDatabase($type){
    global $db_LINE;
    $result = $db_LINE->select("SELECT * FROM `line_event_type` WHERE `event_type` = ?", [$type]);
    return $result;
}





?>