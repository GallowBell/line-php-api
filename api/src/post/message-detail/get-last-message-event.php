<?php 

function APIgetLastMessageEvent(){
    global $db_LINE;

    $fields = [
        'LEMD.id as message_detail_id',
        'LEMD.quoteToken',
        'LEMD.text',
        'LEMD.type as message_type',
        'LWH.id as webhook_id',
        'LWH.datetime',
        'LWH.userId',
        'LWH.type as webhook_type',
        'LU.displayName',
        'LU.pictureUrl',
    ];

    $fields = implode(', ', $fields);

    $sql = "SELECT {$fields} FROM `line_event_message_detail` LEMD LEFT JOIN line_web_hook LWH ON LWH.id = LEMD.webhook_id AND LWH.`type` = '1' INNER JOIN line_user LU ON LU.userId = LWH.userId ORDER BY LWH.id desc;";

    $result = $db_LINE->select($sql);

    return json_encode([
        'status' => 200,
        'message' => 'success',
        'data' => $result
    ]);

}

function APIgetLastMessageEventGroupByUser(){
    global $db_LINE;

    $fields = [
        'LEMD.id as message_detail_id',
        'LEMD.quoteToken',
        'LEMD.text',
        'LEMD.type as message_type',
        'LWH.id as webhook_id',
        'LWH.datetime',
        'LWH.userId',
        'LWH.type as webhook_type',
        'LU.displayName',
        'LU.pictureUrl',
    ];

    $fields = implode(', ', $fields);

    $sql = "SELECT {$fields} FROM `line_event_message_detail` LEMD LEFT JOIN line_web_hook LWH ON LWH.id = LEMD.webhook_id AND LWH.`type` = '1' INNER JOIN line_user LU ON LU.userId = LWH.userId Group By LU.userId ORDER BY LWH.id desc;";

    $result = $db_LINE->select($sql);

    return json_encode([
        'status' => 200,
        'message' => 'success',
        'data' => $result
    ]);
}

?>