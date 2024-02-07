<?php 

function APIgetNumberOfMessageDeliveries($verify){
    global $db_LINE;

    header('Content-type: application/json; charset=utf-8');
    $access_level = $verify->access_level;
    if($access_level < 100){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }

    require_once __DIR__ . '/../../../../webhook/insight.php';

    $date = $_POST['date']?$_POST['date']:date('Ymd');

    //define redis key
    $key_redis = "getNumberOfMessageDeliveries()";
    $params = [
        $date
    ];
    $result = $db_LINE->getRedisData($key_redis, $params);
    if(!!$result){
        return json_encode($result);
    }
    
    $result = getNumberOfMessageDeliveries($date);

    $db_LINE->setRedisCacheTime(60);
    $db_LINE->setRedisData($key_redis, $params, $result);

    return json_encode($result);

}

?>