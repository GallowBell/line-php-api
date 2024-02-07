<?php 

function getNumberFollowers($verify){
    global $db_LINE;

    header('Content-type: application/json; charset=utf-8');
    $access_level = $verify->access_level;
    if($access_level < 100){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }


    $unixtime = time()-60;

    $data = $db_LINE->select("SELECT * FROM `line_number_follower` WHERE `last_update_unix` >= ? ORDER BY `id` DESC LIMIT 1;", [$unixtime]);

    if(count($data) == 1){
        return json_encode([
            'status' => 200,
            'message' => 'OK',
            'data' => $data[0]
        ]);
    }

    require_once __DIR__ . '/../../../../webhook/insight.php';

    $date = date('Ymd');

    $result = getNumberOfFollowers($date);

    if($result['status'] != 200){
        return json_encode($result);
    }

    $data = $result['data'];

    $status = $data['status'];
    $followers = $data['followers'];
    $targetedReaches = $data['targetedReaches'];
    $blocks = $data['blocks'];

    $db_LINE->insert("INSERT INTO `line_number_follower` (
            `status`,
            `followers`,
            `targetedReaches`,
            `blocks`,
            `last_update_unix`,
            `last_update`
        ) VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        );", 
        [
            $status,
            $followers,
            $targetedReaches,
            $blocks,
            time(),
            date('Y-m-d H:i:s')
        ]
    );

    $result['data']['last_update'] = date('Y-m-d H:i:s');

    return json_encode($result);
    
}

?>