<?php 

$CHANEL_ACCESS_TOKEN = $_ENV['LINE_BOT_CHANNEL_ACCESS_TOKEN'];


use \LINE\Clients\MessagingApi\Model\TextMessage;
use \LINE\Clients\MessagingApi\Model\FlexMessage;
use \LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use \LINE\Clients\MessagingApi\Model\PushMessageRequest;
use \LINE\Constants\MessageType;
use \LINE\Clients\MessagingApi\Api\MessagingApiApi;
use \LINE\Clients\MessagingApi\Configuration;
use \LINE\Clients\MessagingApi\Api\MessagingApiBlobApi;

use \GuzzleHttp\Client;
use \LINE\Clients\MessagingApi\ApiException ;

//function generate UUID using timestamp

$client = new Client();
$config = new Configuration();
$config->setAccessToken($CHANEL_ACCESS_TOKEN);

$messagingApi = new MessagingApiApi(
  $client,
  $config,
);

$messageBlobApi = new MessagingApiBlobApi(
  $client,
  $config,
);

require_once __DIR__ . '/insight.php';

/**
 * Push message
 * @param string $userId
 * @param TextMessage|FlexMessage|array $message
 * @return string
 */
function pushMessage($userId, $message){
  global $messagingApi;
  try {
    $request = new PushMessageRequest([
        'to' => $userId,
        'messages' => [$message],
    ]);

    $xLineRetryKey = generate_uuid();
    $response = $messagingApi->pushMessage($request, $xLineRetryKey, "application/json");
    save_log('pushMessage response => '.$response);

    return $response;

  }catch(ApiException $e) {

    $error = " LINE pushMessage ERROR " . $e;
    $error_2 = " LINE replyMessage ERROR Object ". $e->getResponseBody();

    save_log($error);
    save_log($error_2);


    return  [
      'status' => 401,
      'message' => $e->getResponseBody()
    ];

  }
}

/**
 * Reply message
 * @param string $replyToken
 * @param TextMessage|FlexMessage|array $message
 * @return string
 */
function replyMessage($replyToken, $message){
  global $messagingApi;
  try {
    $request = new ReplyMessageRequest([
        'replyToken' => $replyToken,
        'messages' => $message,
    ]);
    
    $response = $messagingApi->replyMessage($request, "application/json");
    save_log('replyMessage response => '.$response);
    return $response;
  
  }catch(ApiException $e) {

    $error = " LINE replyMessage ERROR " . $e;
    $error_2 = " LINE replyMessage ERROR Object ". $e->getResponseBody();

    save_log($error);
    save_log($error_2);

    return [
      'status' => 500,
      'message' => $e->getResponseBody()
    ];

  }
}

function getLINEFollowers(){
  global $messagingApi;
  try {
    $response = $messagingApi->getFollowers("application/json");
    save_log('LINE getFollowers => '. json_encode($response));
    return $response;
  
  }catch(ApiException $e) {

    $error = " LINE getLINEFollowers ERROR " . $e;
    $error_2 = " LINE getLINEFollowers ERROR Object ". $e->getResponseBody();

    save_log($error);
    save_log($error_2);

    return [
      'status' => 500,
      'message' => $e->getResponseBody()
    ];
    
  }
}

/**
 * Get number of sent reply messages
 * @param string $date Date the messages were sent  Format: &#x60;yyyyMMdd&#x60; (e.g. &#x60;20191231&#x60;) Timezone: UTC+9 (required)
 * @throws \InvalidArgumentException
 */
function getNumberOfSentReplyMessagesRequest($date=''){
  global $messagingApi;
  try {
    $contentTypes = $messagingApi::contentTypes['getNumberOfSentReplyMessages'][0];
    $response = $messagingApi->getNumberOfSentReplyMessagesWithHttpInfo($date, $contentTypes);
    save_log('LINE getNumberOfSentReplyMessagesRequest => '. json_encode($response));
    return [
      'status' => 200,
      'message' => 'OK',
      'data' => $response[0]
    ];
  
  }catch(ApiException $e) {

    $error = " LINE getNumberOfSentReplyMessagesRequest ERROR " . $e;
    $error_2 = " LINE getNumberOfSentReplyMessagesRequest ERROR Object ". $e->getResponseBody();

    save_log($error);
    save_log($error_2);

    return [
      'status' => 500,
      'message' => $e->getResponseBody()
    ];
    
  }
}

function getMessageQuotaRequest(){
  global $messagingApi;
  try {
    $contentTypes = $messagingApi::contentTypes['getMessageQuota'][0];
    $response = $messagingApi->getMessageQuotaWithHttpInfo($contentTypes);
    save_log('LINE getMessageQuotaRequest => '. json_encode($response));
    return [
      'status' => 200,
      'message' => 'OK',
      'data' => $response[0]
    ];
  
  }catch(ApiException $e) {

    $error = " LINE getMessageQuotaRequest ERROR " . $e;
    $error_2 = " LINE getMessageQuotaRequest ERROR Object ". $e->getResponseBody();

    save_log($error);
    save_log($error_2);

    return [
      'status' => 500,
      'message' => $e->getResponseBody()
    ];
    
  }
}

function getBotInfo(){
    global $messagingApi;
    try {
      $contentType = $messagingApi::contentTypes['getBotInfo'][0];
      $result = $messagingApi->getBotInfoWithHttpInfo($contentType);
      save_log('LINE getBotInfo => '. json_encode($result));
      return [
        'status' => 200,
        'message' => 'OK',
        'data' => $result[0]
      ];
    }catch(ApiException $e) {
  
      $error = " LINE getMessageQuotaRequest ERROR " . $e;
      $error_2 = " LINE getMessageQuotaRequest ERROR Object ". $e->getResponseBody();
  
      save_log($error);
      save_log($error_2);
  
      return [
        'status' => 500,
        'message' => $e->getResponseBody()
      ];
      
    }
}

function getGroupSummary($groupId=''){
  global $messagingApi;

  if($groupId == '') {
    return false;
  }

  try {
    $result = $messagingApi->getGroupSummary($groupId);
    save_log('LINE getGroupSummary => '. json_encode($result));
    return [
      'status' => 200,
      'message' => 'OK',
      'data' => $result
    ];
  }catch(ApiException $e) {
      
      $error = " LINE getGroupSummary ERROR " . $e;
      $error_2 = " LINE getGroupSummary ERROR Object ". $e->getResponseBody();
  
      save_log($error);
      save_log($error_2);
  
      return [
        'status' => 500,
        'message' => $e->getResponseBody()
      ];
      
    }
}

function getMessageContentWithHttpInfo($message_id=''){
  global $messageBlobApi;
  try {
    
    save_log('LINE getMessageContent message_id => '. json_encode($message_id));
    if($message_id == '') {
      return false;
    }

    $result = $messageBlobApi->getMessageContentWithHttpInfo($message_id);

    save_log('LINE getMessageContent => '. json_encode($result));

    return $result;

  }catch(ApiException $e) {

    $error = " LINE getMessageContent ERROR " . $e;
    $error_2 = " LINE getMessageContent ERROR Object ". $e->getResponseBody();

    save_log($error);
    save_log($error_2);

    return $e->getResponseBody();
    
  }
  
}

function getMessageContentPreviewWithHttpInfo($message_id=''){
  global $messageBlobApi;
  try {
    
    save_log('LINE MessageContentPreview message_id => '. json_encode($message_id));
    if($message_id == '') {
      return false;
    }

    $result = $messageBlobApi->getMessageContentPreviewWithHttpInfo($message_id);

    save_log('LINE MessageContentPreview => '. json_encode($result));

    return $result;

  }catch(ApiException $e) {

    $error = " LINE MessageContentPreview ERROR " . $e;
    $error_2 = " LINE MessageContentPreview ERROR Object ". $e->getResponseBody();

    save_log($error);
    save_log($error_2);

    return $e->getResponseBody();
    
  }
}


?>