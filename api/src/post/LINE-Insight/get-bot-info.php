<?php 

function APIgetBotInfo(){
    global $db_LINE;

    require_once __DIR__ . '/../../../../webhook/config.php';

    $date = md5(date('YmdHi'));

    //define redis key
    $key_redis = "getBotInfo()";
    $params = [
        $date
    ];

    //$db_LINE->ClearAllRedis();

    $result = $db_LINE->getRedisData($key_redis, $params);
    if(!!$result){
        return json_encode($result);
    }

    $result = getBotInfo();

    $db_LINE->setRedisCacheTime(60);
    $db_LINE->setRedisData($key_redis, $params, $result);

    return json_encode($result);

}

?>