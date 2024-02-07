<?php 

//require line-bot-message
require_once __DIR__ . '/../../line-bot/line-bot-message.php';

function initEventMessage($events, $webhook_id){

    save_webhook_message($events, $webhook_id);

    $lineBotMessage = LineBotMessage($events, $webhook_id);

    save_log("init event message lineBotMessage => ". $lineBotMessage);

}

//function save webhook message
function save_webhook_message($events, $webhook_id){
    global $db_LINE, $messageBlobApi;
    //convert object to array

    $data = $events;
    $replyToken = $data['replyToken'];
    $type = $data['type'];
    $timestamp = $data['timestamp'];
    $source = $data['source'];
    $message = $data['message'];
    $message_id = $message['id'];
    $message_type = $message['type'];
    $message_text = $message['text'];
    $message_quoteToken = $message['quoteToken'];

    if($message_type == "image") {

        $message_contentProvider_type = $message['contentProvider']['type'];

        $result_saveOriginal = saveImageContentOriginal($message_id);
        $result_savePreview = saveImageContentPreview($message_id);

        save_log('result_save => '. json_encode($result_saveOriginal));

        $fileExtension = $result_saveOriginal['fileExtension'];
        $fileSize = $result_saveOriginal['fileSize'];
        $file_name = $result_saveOriginal['file_name'];

    }

    /*     
    $message_packageId = null;
    $message_stickerId = null;
    $stickerResourceType = null; 
    */

    if($message_type == "sticker"){
        $message_packageId = $message['packageId'];
        $message_stickerId = $message['stickerId'];
        $stickerResourceType = $message['stickerResourceType'];
    }

    /* 
    $message_location = $message['location'];
    $message_title = $message_location['title'];
    $message_address = $message_location['address'];
    $message_latitude = $message_location['latitude'];
    $message_longitude = $message_location['longitude'];
    $message_packageId = $message['packageId']; 
    */

    $message_type_db = getEventMessageDatabase($message_type);

    save_log('$message_type_db => ' . json_encode($message_type_db));

    $message_type_id = $message_type_db[0]['id'];

    save_log(json_encode($data));

    $sql = "INSERT INTO `line_event_message_detail` (
        `webhook_id`,
        `line_message_id`,
        `replyToken`,
        `quoteToken`,
        `text`,
        `type`,
        `packageId`,
        `stickerId`,
        `stickerResourceType`,
        `contentProvider_type`,
        `fileName`,
        `fileSize`,
        `file_extension`
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
        $webhook_id,
        $message_id,
        $replyToken,
        $message_quoteToken,
        $message_text,
        $message_type_id,
        $message_packageId,
        $message_stickerId,
        $stickerResourceType,
        $message_contentProvider_type,
        $file_name,
        $fileSize,
        $fileExtension
    ];

    save_log('parameter insert msg detail => '.json_encode($parameter));

    $result = $db_LINE->insert($sql, $parameter);

    save_log('save message detail => '.json_encode($result));

    /* if($type == 'message') {
        $sql = "INSERT INTO `line_event_message_detail` (
            `webhook_id`,
            `line_message_id`,
            `replyToken`,
            `quoteToken`,
            `text`,
            `type`
        ) VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )";

        $parameter = [
            $webhook_id,
            $message_id,
            $replyToken,
            $message_quoteToken,
            $message_text,
            $message_type_id
        ];

        save_log('parameter insert msg detail => '.json_encode($parameter));

        $result = $db_LINE->insert($sql, $parameter);

        save_log('save message detail => '.json_encode($result));

    } */

}

$onMatch = function($pattern) {
    //echo 'Match found!';
    return $pattern;
};

$onNoMatch = function($pattern) {
    //echo 'Match not found.';
    return $pattern;
};

function matchesPattern($string, $pattern, $onMatch, $onNoMatch) {
    if (preg_match($pattern, $string) === 1) {
        $onMatch($pattern);
    } else {
        $onNoMatch($pattern);
    }
}

function getEventMessageDatabase($type){
    global $db_LINE;
    $result = $db_LINE->select("SELECT * FROM `line_event_message_type` WHERE `message_type` = ?", [$type]);
    return $result;
}

function saveImageContentOriginal($message_id=''){
    
    try {

        $MessageContentWithHttpInfo = getMessageContentWithHttpInfo($message_id);

        if (!is_array($MessageContentWithHttpInfo)) {
            throw new Exception('getMessageContentWithHttpInfo error => '. $MessageContentWithHttpInfo);
        }

        $result_content = $MessageContentWithHttpInfo[0];

        $ContentHttpInfo = $MessageContentWithHttpInfo[2];

        save_log('ContentHttpInfo => '. json_encode($ContentHttpInfo));

        save_log('ContentHttpInfo => '. $ContentHttpInfo['Content-Type'][0]);

        $file_content = '';

        // Get file extension
        $fileExtension = explode('/', $ContentHttpInfo['Content-Type'][0])[1];

        $fileSize = $ContentHttpInfo['Content-Length'][0];      

        $file_name = $message_id.'.'.$fileExtension;

        save_log('fileSize => '.$fileSize);

        save_log('fileExtension => '. $fileExtension);  

        save_log('file_name => '. $file_name);

        //check folder is exists
        if (!file_exists(__DIR__ . '/../../../media/img/original/')) {
            mkdir(__DIR__ . '/../../../media/img/original/', 0777, true);
        }

        // Create SplFileObject for writing
        $writeFile = new SplFileObject(__DIR__ . '/../../../media/img/original/'.$file_name, 'w');

        while (!$result_content->eof()) {
            $writeFile->fwrite($result_content->fgets());
        }

        return [
            'fileExtension' => $fileExtension,
            'fileSize' => $fileSize,
            'file_name' => $file_name,
        ];

    } catch (Exception $e) {
        save_log('saveImageContentOriginal error => '. $e->getMessage());
        return false;
    }
}

function saveImageContentPreview($message_id=''){
    //getMessageContentPreviewWithHttpInfo
    try {

        $MessageContentWithHttpInfo = getMessageContentPreviewWithHttpInfo($message_id);

        if (!is_array($MessageContentWithHttpInfo)) {
            throw new Exception('getMessageContentWithHttpInfo error => '. $MessageContentWithHttpInfo);
        }

        $result_content = $MessageContentWithHttpInfo[0];

        $ContentHttpInfo = $MessageContentWithHttpInfo[2];

        save_log('ContentHttpInfo => '. json_encode($ContentHttpInfo));

        save_log('ContentHttpInfo => '. $ContentHttpInfo['Content-Type'][0]);

        $file_content = '';

        // Get file extension
        $fileExtension = explode('/', $ContentHttpInfo['Content-Type'][0])[1];

        $fileSize = $ContentHttpInfo['Content-Length'][0];      

        $file_name = $message_id.'.'.$fileExtension;

        save_log('fileSize => '.$fileSize);

        save_log('fileExtension => '. $fileExtension);  

        save_log('file_name => '. $file_name);

        //check folder is exists
        if (!file_exists(__DIR__ . '/../../../media/img/preview/')) {
            mkdir(__DIR__ . '/../../../media/img/preview/', 0777, true);
        }

        // Create SplFileObject for writing
        $writeFile = new SplFileObject(__DIR__ . '/../../../media/img/preview/'.$file_name, 'w');

        while (!$result_content->eof()) {
            $writeFile->fwrite($result_content->fgets());
        }

        return [
            'fileExtension' => $fileExtension,
            'fileSize' => $fileSize,
            'file_name' => $file_name,
        ];

    } catch (Exception $e) {
        save_log('saveImagePreviewContent error => '. $e->getMessage());
        return false;
    }
}





?>