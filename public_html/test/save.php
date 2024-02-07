<?php 

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/line_notify.php';

header('Content-type: application/json; charset=utf-8');

//chekck authorization
if(!isset($_SERVER['HTTP_AUTHORIZATION'])){
    http_response_code(401);
    echo json_encode([
        'status' => 401,
        'message' => 'Unauthorized'
    ]);
    exit;
}

/**
 * * TOKEN LINE
 * @var string $token
 */
$token = explode(' ', $_SERVER['HTTP_AUTHORIZATION'])[1];

/**
 * MIN TEMP for alert line notify
 * @var float $MIN_TEMP
 */
$MIN_TEMP = (float)$_ENV['MIN_TEMP'];


/**
 * MAX TEMP for alert line notify
 * @var float $MAX_TEMP
 */
$MAX_TEMP = (float)$_ENV['MAX_TEMP'];

$stickerPackageId = $_ENV['stickerPackageId'];
$stickerId = $_ENV['stickerId'];

if($token !== "bG50rNm9DSXtvscUeI3YS6IbzawOThfzfP4pv6YHWaj"){
    http_response_code(401);
    echo json_encode([
        'status' => 401,
        'message' => 'Unauthorized'
    ]);
    exit;
}

$device = $_POST['device'];
$temp = $_POST['temp'];
$mac_address = $_POST['mac_address'];
$date_time = date('Y-m-d H:i:s');

$device_explode = explode('-', $device);
$hospcode = $device_explode[0];
$number = $device_explode[1];

$sql = "INSERT INTO `iot_temp` (
    `device_id`,
    `temp`,
    `mac_address`,
    `hospcode`,
    `number`,
    `date_time`
) VALUES (
    ?,
    ?,
    ?,
    ?,
    ?,
    ?
);";

$prepared = [
    $device,
    $temp,
    $mac_address,
    $hospcode,
    $number,
    $date_time,
];

$iot_temp_id = $db_Temp->insert($sql, $prepared);
$headers = apache_request_headers();
$method = $_SERVER['REQUEST_METHOD'];
$header_json = $headers;
$body_json = $_REQUEST;

$authorization = $header_json['Authorization'];
$length = $header_json['Content-Length'];
$type = $header_json['Content-Type'];
$host = $header_json['Host'];
$user_agent = $header_json['User-Agent'];


$prepared_header = [
    $iot_temp_id,
    $authorization,
    $length,
    $type,
    $host,
    $user_agent,
    $method
];

$sql_header = "INSERT INTO `api_save_log` (
                                            `iot_temp_id`,
                                            `authorization`,
                                            `length`,
                                            `type`,
                                            `host`,
                                            `user_agent`,
                                            `method`
                                        ) VALUES (
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?
                                        );";

$db_Temp->insert($sql_header, $prepared_header);

if($temp > $MAX_TEMP ){

    $warn_msg = "พบอุณหภูมิมากกว่าที่กำหนด {$temp}°C";
    $messages = "
    ⚠ แจ้งเตือนผิดปกติ
    {$warn_msg}
    วันที่: {$date_time}
    ";

    $messages = str_replace("
    ", "\n", $messages);

    send_line_notify($token, [
        'message' => $messages,
        "stickerPackageId" => $stickerPackageId,
        "stickerId" => $stickerId
    ]);
}

if($temp < $MIN_TEMP ){

    $warn_msg = "พบอุณหภูมิต่ำกว่าที่กำหนด {$temp}°C";
    $messages = "
    ⚠ แจ้งเตือนผิดปกติ
    {$warn_msg}
    วันที่: {$date_time}
    ";

    $messages = str_replace("
    ", "\n", $messages);

    send_line_notify($token, [
        'message' => $messages,
        "stickerPackageId" => $stickerPackageId,
        "stickerId" => $stickerId
    ]);
}

/* 
 ⚠ แจ้งเตือนผิดปกติ 
 พบอุณหภูมิสูงกว่าที่กำหนด 27.06°
 วันที่: 15 มกราคม 2024
 ที่เวลา: 09:43:32
*/

echo json_encode([
    'status' => 200,
    'message' => 'OK'
]);

?>