<?php 

/* phpinfo();

return; */

error_reporting(E_ALL);

//autoload
require_once __DIR__ . '/../../../vendor/autoload.php';

require_once __DIR__ . '/../../../connection/connection_redis.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$db_Temp = new Connection([
    'DB_HOST' => $_ENV['DB_HOST'],
    'DB_USERNAME' => $_ENV['DB_USERNAME'],
    'DB_PASSWORD' => $_ENV['DB_PASSWORD'],
    'DB_NAME' => $_ENV['DB_NAME'],
    'DB_CHARSET' => $_ENV['DB_CHARSET'],
    'DEBUG_MODE' => true
]);
$db_Temp->connect();

$TOKEN_LIST = [
    //NVK - นวนคร
    "bG50rNm9DSXtvscUeI3YS6IbzawOThfzfP4pv6YHWaj"
];

header('Content-type: application/json; charset=utf-8');

$action = $_GET['action'] ?? false;

// Usage
if (!checkRateLimit()) {
    http_response_code(429);
    echo json_encode([
        'status' => 429,
        'message' => 'Too Many Requests',
    ]);
    exit;
}

if(!$action){
    echo json_encode([
        'status' => 400,
        'message' => 'Bad Request',
    ]);
    exit;
}

function checkRateLimit($limit = 90, $timeFrame = 60) {
    session_start();

    // Initialize the session variables if they don't exist
    if (!isset($_SESSION['request_count'])) {
        $_SESSION['request_count'] = 0;
        $_SESSION['first_request'] = time();
    }

    // If the time frame has passed, reset the session variables
    if (time() - $_SESSION['first_request'] >= $timeFrame) {
        $_SESSION['request_count'] = 0;
        $_SESSION['first_request'] = time();
    }

    // Increment the request count
    $_SESSION['request_count']++;

    // If the request count exceeds the limit, return false
    if ($_SESSION['request_count'] > $limit) {
        return false;
    }

    return true;
}

function groupBy($array, $key) {
    $result = array();
    foreach($array as $val) {
        if(array_key_exists($key, $val)){
            $result[$val[$key]][] = $val;
        }else{
            $result[""][] = $val;
        }
    }

    return $result;
}

function getTempByDate($parameter=[]){
    global $db_Temp;

    $date_start = $parameter['date_start'] ? $parameter['date_start'] : false;
    $date_end = $parameter['date_end'] ? $parameter['date_end'] : false;
    $hospcode = $parameter['hospcode'] ? $parameter['hospcode'] : false;
    $number = $parameter['number'] ? $parameter['number'] : false;

    if(!$date_start || !$date_end){
        return json_encode([
            'status' => 400,
            'message' => 'Bad Request',
        ]);
    }

    $sql = "SELECT  * FROM `iot_temp` WHERE `hospcode` = ? AND `number` = ? AND date_time BETWEEN ? AND ? ORDER BY `id` ASC;";

    $parameter = [
        $hospcode,
        $number,
        $date_start,
        $date_end,
    ];

    $result = $db_Temp->select($sql, $parameter);

    $count = 0 ;
    $filter = $_POST['filter'] ?? false;
    $last_hour = '';
    $index = 0;
    $data = [];
    $max = false;
    $min = false;

    foreach ($result as $key => $row) {

        //check filter
        if($filter == 'hours'){
            $date_time = date('H', strtotime($row['date_time']));
            if($date_time == $last_hour){
                unset($data[$index]);
                //$count--;
                continue;
            }
            $last_hour = $date_time;
        }

        if($filter == '30minutes'){
            $date_time = date('i', strtotime($row['date_time']));
            if($date_time % 30 != 0){
                unset($data[$index]);
                //$count--;
                continue;
            }
        }

        if($filter == '15minutes'){
            $date_time = date('i', strtotime($row['date_time']));
            if($date_time % 15 != 0){
                unset($data[$index]);
                //$count--;
                continue;
            }
        }

        //exclude id
        //$data[$index]['id'] = (int)($row['id']);
        unset($data[$index]['id']);
        $data[$index]['temp'] = (float)($row['temp']);

        if($data[$index]['temp'] > $max || !$max){
            $max = $data[$index]['temp'];
        }
        if($data[$index]['temp'] < $min || !$min){
            $min = $data[$index]['temp'];
        }

        //$data[$index]['mac_address'] = $row['mac_address'];
        $data[$index]['date_time'] = $row['date_time'];
        $data[$index]['device_id'] = $row['device_id'];
        $count++;

        
        $index++;
    }
    
    if(count($data) == 0){
        $sql = "SELECT  * FROM `iot_temp` WHERE `hospcode` = ? AND `number` = ? ORDER BY `id` DESC LIMIT 1;";

        $parameter = [
            $hospcode,
            $number
        ];

        $result = $db_Temp->select($sql, $parameter);

        if(count($result) == 0){
            return json_encode([
                'status' => 404,
                'message' => 'Not Found',
                'd' => $result,
                'data' => []
            ]);
        }

        return json_encode([
            'status' => 404,
            'message' => 'Bad Request',
            'data' => [],
            'lastest' => $result[0]['date_time']
        ]);
        
    }

    return json_encode([
        'status' => 200,
        'message' => 'OK',
        'max' => $max,
        'min' => $min,
        'data' => $data,
        'total' => $count
    ]);
}

if ($action == "getTempByDate") {
    $result = getTempByDate($_POST);
    echo $result;
    exit;
}

if($action == "getHospcode"){
    $sql = "SELECT * FROM `hospcode` WHERE `status` = 1 ORDER BY `hospcode` ASC;";
    $result = $db_Temp->select($sql, $parameter);
    echo json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result,
    ]);
    exit;
}

if($action == "getLastTemp"){

    $hospcode = $_POST['hospcode'] ?? false;
    if(!$hospcode){
        echo json_encode([
            'status' => 400,
            'message' => 'Bad Request',
        ]);
        exit;
    }

    $sql = "SELECT 
            it.*,
            h.`name`
        FROM `iot_temp` it
        INNER JOIN (
            SELECT `hospcode`, `number`, MAX(`id`) as MaxID
            FROM `iot_temp`
            WHERE `hospcode` = ?
            GROUP BY `hospcode`, `number`
        ) grouped_it ON it.`hospcode` = grouped_it.`hospcode` AND it.`number` = grouped_it.`number` AND it.`id` = grouped_it.MaxID
        INNER JOIN `device_list` DL ON DL.device_id = it.`device_id` AND DL.is_active = 1
        LEFT JOIN `hospcode` h ON h.`namehos` = it.`hospcode`
        ORDER BY it.`id` DESC;";
    
    $parameter = [
        $hospcode
    ];

    $result = $db_Temp->select($sql, $parameter);
    echo json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result,
    ]);
    exit;
}

if($action == "getLastTempAll"){

    $sql = "SELECT 
                it.*,
                h.`name` 
            FROM `iot_temp` it 
            LEFT JOIN `hospcode` h ON h.`namehos` = it.`hospcode` 
            INNER JOIN `device_list` DL ON DL.device_id = it.`device_id` AND DL.is_active = 1
            INNER JOIN ( 
                SELECT 
                    `hospcode`,
                    `number`,
                    MAX(`id`) as MaxID 
                FROM `iot_temp` 
                GROUP BY `hospcode`, `number` 
            ) grouped_it ON it.`hospcode` = grouped_it.`hospcode` AND it.`number` = grouped_it.`number` AND it.`id` = grouped_it.MaxID 
            ORDER BY it.`id` DESC;";

    $data = $db_Temp->select($sql);
    $result = [];
    $last_hospcode = $data[0]['hospcode'];
    $last_num = $data[0]['number'];
    $result = groupBy($data, 'hospcode');

    echo json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result,
        'r' => $data
    ]);
    exit;
}

if($action == "getDeviceByHosname"){
    
    $sql = "SELECT * FROM `device_list` WHERE hospcode = ? AND is_active = 1";
    $parameter = [
        $_POST['hospcode']
    ];

    $result = $db_Temp->select($sql, $parameter);

    echo json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result,
    ]);
    exit;
}

//get devicelist
if($action == "getDeviceList") {
    $sql = "SELECT DL.*, h.namehos, h.name FROM `device_list` DL LEFT JOIN `hospcode` h ON `h`.`namehos` = `DL`.`hospcode` ORDER BY `DL`.`hospcode` ASC;";
    $result = $db_Temp->select($sql);

    if(!$result){
        echo json_encode([
            'status' => 500,
            'message' => 'Internal Server Error',
            'data' => $result
        ]);
        exit(500);
    }

    echo json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result,
    ]);
    exit;
}


//update status `is_active` in `device_list`
if($action == "changeStatus") {

    $is_active = $_POST['is_active'];
    $id = $_POST['id'];

    if(!isset($is_active) || !isset($id)){
        echo json_encode([
            'status' => 400,
            'message' => 'Bad Request',
        ]);
        exit;
    }

    if(empty($is_active) || empty($id)){
        echo json_encode([
            'status' => 400,
            'message' => 'Bad Request',
            'e' => empty($is_active)
        ]);
        exit;
    }

    $sql = "UPDATE `device_list` SET `is_active` = ? WHERE `id` = ?;";

    $is_active = $is_active == 'true' ? 1 : 0;

    $parameter = [
        $is_active,
        $id,
    ];

    $result = $db_Temp->update($sql, $parameter);

    if(!$result){
        echo json_encode([
            'status' => 500,
            'message' => 'Internal Server Error',
        ]);
        exit(500);
    }

    echo json_encode([
        'status' => 200,
        'message' => 'OK',
    ]);
    exit;
}

if($action == "AddNewDevice"){

    $name = $_POST['name'];
    $mac_address = $_POST['mac_address'];
    $hospcode = $_POST['hospcode'];
    $number = $_POST['number'];

    $parameter = [
        $name,
        $mac_address,
        $hospcode,
        $number,
        '1'
    ];    

    $sql = "INSERT INTO `device_list` (
                                        `device_id`,
                                        `mac_address`,
                                        `hospcode`,
                                        `number`,
                                        `is_active`
                                    ) VALUES (
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?
                                    )";

    $result = $db_Temp->insert($sql, $parameter);

    if(!$result){
        echo json_encode([
            'status' => 500,
            'message' => 'Internal Server Error',
        ]);
        exit(500);
    }

    echo json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result
    ]);
    exit;
}

if($action == "EditDevice"){

    $id = $_POST['id'];
    $name = $_POST['name'];
    $mac_address = $_POST['mac_address'];
    $hospcode = $_POST['hospcode'];
    $number = $_POST['number'];

    $parameter = [
        $name,
        $mac_address,
        $hospcode,
        $number,
        $id
    ];

    $sql = "UPDATE `device_list` SET 
                                    `device_id` = ?,
                                    `mac_address` = ?,
                                    `hospcode` = ?,
                                    `number` = ?
                                WHERE `id` = ?";

    $result = $db_Temp->update($sql, $parameter);

    if(!$result){
        echo json_encode([
            'status' => 500,
            'message' => 'Internal Server Error',
        ]);
        exit(500);
    }

    echo json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result
    ]);
    exit;

}


?>

