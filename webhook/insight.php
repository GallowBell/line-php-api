<?php 

$CHANEL_ACCESS_TOKEN = $_ENV['LINE_BOT_CHANNEL_ACCESS_TOKEN'];

use \LINE\Clients\Insight\Api\InsightApi;
use \LINE\Clients\Insight\Configuration;
use \GuzzleHttp\Client;
use \LINE\Clients\Insight\ApiException;


$client = new Client();
$config = new Configuration();
$config->setAccessToken($CHANEL_ACCESS_TOKEN);

$insightApi = new InsightApi($client, $config);


function getFriendsDemographics() {
    global $insightApi;
    try {
        $result = $insightApi->getFriendsDemographics();
        return [
            'status' => 200,
            'message' => 'OK',
            'data' => $result
        ];
    }catch(ApiException $e) {
        return [
            'status' => 500,
            'message' => 'Internal Server Error',
            'error' => json_decode($e->getResponseBody())
        ];
    }
    
}

function getMessageEvent($requestId = null) {
    global $insightApi;

    try {
        $result = $insightApi->getMessageEvent($requestId);
        return [
            'status' => 200,
            'message' => 'OK',
            'data' => $result
        ];
    }catch(ApiException $e) {
        return [
            'status' => 500,
            'message' => 'Internal Server Error',
            'error' => json_decode($e->getResponseBody())
        ];
    }
}

function getNumberOfFollowers($date='') {
    global $insightApi;
    
    try {
        $result = $insightApi->getNumberOfFollowers($date);
        return [
            'status' => 200,
            'message' => 'OK',
            'data' => $result
        ];
    }catch(ApiException $e) {
        return [
            'status' => 500,
            'message' => 'Internal Server Error',
            'error' => json_decode($e->getResponseBody())
        ];
    }
}

function getNumberOfMessageDeliveries($parameterDate='') {
    global $insightApi;
    //date format: 20191231
    $date = $parameterDate?$parameterDate:date('Ymd');
    try {
        $result = $insightApi->getNumberOfMessageDeliveries($date);
        return [
            'status' => 200,
            'message' => 'OK',
            'data' => $result
        ];
    }catch(ApiException $e) {
        return [
            'status' => 500,
            'message' => 'Internal Server Error',
            'error' => json_decode($e->getResponseBody())
        ];
    }
}

/* function getStatisticsPerUnit() {
    global $insightApi;
    $insightApi->getStatisticsPerUnit();
} */


?>