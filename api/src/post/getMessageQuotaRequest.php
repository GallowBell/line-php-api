<?php 

function APIgetMessageQuotaRequest($verify){

    header('Content-type: application/json; charset=utf-8');

    $access_level = $verify->access_level;
    if($access_level < 10){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }

    require_once __DIR__ . '/../../../webhook/config.php';
    
    $result = getMessageQuotaRequest();
    return json_encode($result);
}

?>