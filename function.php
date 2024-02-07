<?php 

session_start();

date_default_timezone_set('Asia/Bangkok');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TooManyRedirectsException;

function save_log($value){
    global $db_LINE;

    if ($_ENV['APP_DEBUG'] == true) {
        $db_LINE->insert("INSERT INTO `test`(`text`) VALUES (?)", [$value]);
    }

}

function API_Credit(){

    $client = new Client();
    $response = $client->request('GET', 'https://jsonplaceholder.typicode.com/todos/1');
    save_log("getStatusCode=> ".  $response->getStatusCode() ); // 200
    save_log("getHeaderLine=> ".  $response->getHeaderLine('content-type') ); // 'application/json; charset=utf8'
    save_log("getBody=> ".  $response->getBody() ); // '{"id": 1420053, "name": "guzzle", ...}'

}

function getDataCredit($data = []){
    global $db_LINE;

    $cid = $data['cid']?$data['cid']:"";
    $userId = $data['userId']?$data['userId']:"";

    save_log("getDataCredit displayName => ". $data['displayName']);

    if(!isset($cid) && empty($cid)) {
        return [];
    }
 
    $result = $db_LINE->select("SELECT * FROM `line_user` LI INNER JOIN mmt_credit MC ON MC.cid = LI.cid WHERE LI.userId = ?  ;", [$userId ]);

    save_log("getDataCredit result => ". json_encode($result));

    //$db_Credit->disconnect();
    
    return $result;
}


function getCaption($text, $dbMessageType) {
    global $db_LINE;

    save_log("getCaption dbMessageType => ". $dbMessageType);

    //$caption = $db_LINE->select(" SELECT * FROM `line_bot_caption` WHERE `event_type` = ? AND `caption` = ? ", [$dbMessageType, $text]);
    $caption = $db_LINE->select("SELECT * FROM `line_bot_caption` WHERE `event_type` = ? AND ? REGEXP `caption` AND active = ?;", [$dbMessageType, $text, 1]);

    save_log("getCaption caption => ". json_encode($caption));

    return $caption;
}

function getLINE_BOT_Response($id){
    global $db_LINE;
    $LINE_Response = $db_LINE->select(" SELECT * FROM `line_bot_response` WHERE id = ? AND active = ? ", [$id, 1]);

    save_log('$LINE_Response => ' . json_encode($LINE_Response));

    $is_use_time = $LINE_Response[0]['is_use_time'];
    
    if($is_use_time == 1){  
        $start_time = $LINE_Response[0]['start_time'];
        $end_time = $LINE_Response[0]['end_time'];  

        save_log('curtime =>' . date('H:i'));
        save_log('$start_time => ' . $start_time);
        save_log('$end_time => ' . $end_time);
        save_log('!isTimeInRange' . !isTimeInRange($start_time, $end_time));
        save_log('isTimeInRange' . isTimeInRange($start_time, $end_time));

        if(!isTimeInRange($start_time, $end_time)){
            return [];
        }
    }

    return $LINE_Response;
}

function getDBMessageType($events){
    global $db_LINE;

    $message = $events['message'];
    $type = $message['type'];
    $result = $db_LINE->select("SELECT * FROM `line_event_message_type` WHERE `message_type` = ?", [$type]);
    return $result;
}

//function get client ip
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
    $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
    $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
    $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
    $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
    $ipaddress = getenv('REMOTE_ADDR');
    else
    $ipaddress = 'UNKNOWN';
    return $ipaddress;
}


/**
 * Generate UUID
 * @return string
 */
function generate_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
  
        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),
  
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,
  
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,
  
        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
  }

function selectUserData($id) {
    global $db_LINE;

    save_log('selectUserData '.$id);

    $result = $db_LINE->select("SELECT *, LU.id as id, LL.id as login_id FROM `line_user` LU INNER JOIN `line_login` LL ON LL.id = LU.id_line_login WHERE LU.id = ?; ", [$id]);

    return $result;
}

function selectUserDataByCID($cid) {
    global $db_LINE;

    save_log('selectUserData '.$cid);

    $result = $db_LINE->select("SELECT *, LU.id as id, LL.id as login_id FROM `line_user` LU INNER JOIN `line_login` LL ON LL.id = LU.id_line_login WHERE LU.cid = ?; ", [$cid]);

    return $result;
}

function set_cookie($array) {
    foreach ($array as $name => $value) {
        setcookie(
            $name, //name
            $value, //value
            time() + (86400 * $_ENV['COOKIE_DURATION']), //expires 86400 = 1 day
            "/", //path
            $_SERVER['HTTP_HOST'], //domain
            true, //secure
            true //httponly
        );
    }
}

function remove_cookie($array) {
    foreach ($array as $name => $value) {
        setcookie(
            $name, //name
            $value, //value
            time() - 3600, //expires - 1 hour
            "/", //path
            $_SERVER['HTTP_HOST'], //domain
            true, //secure
            true //httponly
        );
    }
}

function verify_route_post($parameter){

    if(empty($parameter['access_token']) || empty($parameter['expires_in']) || empty($parameter['id_line_user'])) {
        return [
            'status' => 401,
            'message' => 'Unauthorized' 
        ];
    }

    $expires_in = $parameter['expires_in'];
    $access_token = $parameter['access_token'];
    $id_line_user = $parameter['id_line_user'];

    save_log("id_line_user verify_route_post => ". $id_line_user);

    $verify = verify_accessToken($access_token);

    if(!$verify){
        return [
            'status' => 401,
            'message' => 'Unauthorized' 
        ];
    }

    /* if($expires_in > time()) {
        return [
            'status' => 200,
            'message' => 'Token is valid' 
        ];

    } */
    
    $UserData = selectUserData($id_line_user);

    save_log("UserData => ". json_encode($UserData));

    if($expires_in < time()) {

        $refresh_token = $UserData[0]['refresh_token'];
        $token_refresh = refreshToken($refresh_token);
        $token_refresh['id'] = $id_line_user;
        update_line_login($token_refresh);
        $expires_in = $token_refresh['expires_in'] + time();

        $payload = [
            'picture' => $UserData[0]['pictureUrl'],
            'expires_in' => $expires_in,
            'name' => $UserData[0]['displayName'],
            'id_line_user' => $id_line_user,
            'access_token' => $token_refresh['access_token']
        ];

        $jwt = encodeJWT($payload);

        set_cookie([
            'token' => $jwt
        ]);

        save_log("token_refresh => ". json_encode($token_refresh) );

        return [
            'status' => 200,
            'message' => 'Refreshed Token', 
        ];

    }

    return [
        'status' => 200,
        'message' => 'OK', 
    ];

}

function verify_accessToken($access_token){

    if(empty($access_token)){
        return false;
    }

    try {

        $client = new Client();
        $response = $client->get($_ENV['LINE_API_URL'].'/oauth2/v2.1/verify?access_token='.$access_token);
        $body = $response->getBody();

        $statusCode = $response->getStatusCode();

        save_log("verify_accessToken body => ". $body);

        if($statusCode !==200){

            save_log("verify_accessToken error ". $body);

            return false;
        }

        $result = json_decode($body, true);
        $client_id = $result["client_id"];

        if($client_id !== $_ENV['LINE_LOGIN_CHANNEL_ID']){
            return false;
        }

        return $result;

    } catch (ClientException $e) {
        $error = "verify_accessToken => Client error: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (ServerException $e) {
        $error = "verify_accessToken => Server error: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (ConnectException $e) {
        $error = "verify_accessToken => Connection error: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (TooManyRedirectsException $e) {
        $error = "verify_accessToken => Too many redirects: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (RequestException $e) {
        $error = "verify_accessToken => Request error: " . $e->getMessage();
        save_log($error);
        return false;
    }


}

//function refresh token line api
function refreshToken($refresh_token) {
    //guzzle to$_ENV['LINE_API_URL']. /oauth2/v2.1/token

    if(empty($refresh_token)){
        return false;
    }

    try {
        $client = new Client([
            'headers' => [ 
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => $_ENV['LINE_LOGIN_CHANNEL_ACCESS_TOKEN']
            ]
        ]);
        
        $response = $client->post($_ENV['LINE_API_URL'].'/oauth2/v2.1/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refresh_token,
                'client_id' => $_ENV['LINE_LOGIN_CHANNEL_ID'],
                'client_secret' => $_ENV['LINE_LOGIN_CHANNEL_SECRET']
            ]
        ]);

        $body = $response->getBody();
        $statusCode = $response->getStatusCode();

        save_log("refreshToken body => ". $body);

        if($statusCode !==200){

            save_log("refreshToken error ". $body);

            return false;
        }

        /* 
        example response
        {
            "access_token": "eyJhbGciOiJIUzI1NiJ9.P2hgZc2DItxbQf9zRV6bacryAdDeP5itMwbXkNGJAL8vdr4KHfVGmDOxf2swjW89rHyZwgJ7ZAxXQe2zLDbmck9jbZqhy9yjfy2JGxkNdgPLTQ-WrSGP22mgps_EZKD-j1KODFhGlr_mgT3L5wYSN0hNWaUh-crtKTjJp-BI2R0.PFkl_vwkRUra5f2LkJ2TN3_kXS0xOnr-FQ4Wr_fkHQE",
            "token_type": "Bearer",
            "refresh_token": "4ConuBkdfGvyS7nsmPhF",
            "expires_in": 2592000,
            "scope": "openid profile"
        }
        
        */

        $result = json_decode($body, true);

        return $result;

    } catch (ClientException $e) {
        $error = "refreshToken => Client error: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (ServerException $e) {
        $error = "refreshToken => Server error: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (ConnectException $e) {
        $error = "refreshToken => Connection error: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (TooManyRedirectsException $e) {
        $error = "refreshToken => Too many redirects: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (RequestException $e) {
        $error = "refreshToken => Request error: " . $e->getMessage();
        save_log($error);
        return false;
    }
}



function update_line_login($parameter) {
    global $db_LINE;

    $id = $parameter["id"];
    $access_token = $parameter["access_token"];
    $refresh_token = $parameter["refresh_token"];
    $expires_in = $parameter["expires_in"];
    $expires_time = $expires_in+time();
    $id_token = $parameter["id_token"];
    $scope = $parameter["scope"];
    $token_type = $parameter["token_type"];

    $result = $db_LINE->update("UPDATE `line_login` 
        SET `access_token` = ?,
        SET `refresh_token` = ?,
        SET `expires_in` = ?,
        SET `id_token` = ?,
        SET `scope` = ?,
        SET `token_type` = ?

     WHERE `line_login`.`id` = ?;", [
        $access_token,
        $refresh_token,
        $expires_time,
        $id_token,
        $scope,
        $token_type,
        $id,
    ]);

    return $result;
}

function deleteAllCookie(){

    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time()-1000);
            setcookie($name, '', time()-1000, '/');
        }
    }

}

function check_id_card($cardid) {
    $num_id = $cardid;
    $group_1 = substr($num_id, 0, 1); // ดึงเอาเลขเลขตัวที่ 1 ของบัตรประชาชนออกมา
    $group_5 = substr($num_id, 12, 12);  // ดึงเอาเลขเลขตัวที่ 13 ของบัตรประชาชนออกมา

    $num1 = $group_1;
    $num2 = substr($num_id, 1, 1); // ดึงเอาเลขเลขตัวที่ 2 ของบัตรประชาชนออกมา
    $num3 = substr($num_id, 2, 1); // ดึงเอาเลขเลขตัวที่ 3 ของบัตรประชาชนออกมา
    $num4 = substr($num_id, 3, 1); // ดึงเอาเลขเลขตัวที่ 4 ของบัตรประชาชนออกมา
    $num5 = substr($num_id, 4, 1); // ดึงเอาเลขเลขตัวที่ 5 ของบัตรประชาชนออกมา
    $num6 = substr($num_id, 5, 1); // ดึงเอาเลขเลขตัวที่ 6 ของบัตรประชาชนออกมา
    $num7 = substr($num_id, 6, 1); // ดึงเอาเลขเลขตัวที่ 7 ของบัตรประชาชนออกมา
    $num8 = substr($num_id, 7, 1); // ดึงเอาเลขเลขตัวที่ 8 ของบัตรประชาชนออกมา
    $num9 = substr($num_id, 8, 1);// ดึงเอาเลขเลขตัวที่ 9 ของบัตรประชาชนออกมา
    $num10 = substr($num_id, 9, 1); // ดึงเอาเลขเลขตัวที่ 10 ของบัตรประชาชนออกมา
    $num11 = substr($num_id, 10, 1);// ดึงเอาเลขเลขตัวที่ 11 ของบัตรประชาชนออกมา
    $num12 = substr($num_id, 11, 1); // ดึงเอาเลขเลขตัวที่ 12 ของบัตรประชาชนออกมา
    $num13 = $group_5;


    // จากนั้นนำเลขที่ได้มา คูณ  กันดังนี้
    $cal_num1 = $num1 * 13; // เลขตัวที่ 1 ของบัตรประชาชน
    $cal_num2 = $num2 * 12; // เลขตัวที่ 2 ของบัตรประชาชน
    $cal_num3 = $num3 * 11; // เลขตัวที่ 3 ของบัตรประชาชน
    $cal_num4 = $num4 * 10; // เลขตัวที่ 4 ของบัตรประชาชน
    $cal_num5 = $num5 * 9; // เลขตัวที่ 5 ของบัตรประชาชน
    $cal_num6 = $num6 * 8; // เลขตัวที่ 6 ของบัตรประชาชน
    $cal_num7 = $num7 * 7; // เลขตัวที่ 7 ของบัตรประชาชน
    $cal_num8 = $num8 * 6; // เลขตัวที่ 8 ของบัตรประชาชน
    $cal_num9 = $num9 * 5; // เลขตัวที่  9  ของบัตรประชาชน
    $cal_num10 = $num10 * 4; // เลขตัวที่ 10 ของบัตรประชาชน
    $cal_num11 = $num11 * 3; // เลขตัวที่ 11 ของบัตรประชาชน
    $cal_num12 = $num12 * 2; // เลขตัวที่ 12 ของบัตรประชาชน


    //นำผลลัพธ์ทั้งหมดจากการคูณมาบวกกัน

    $cal_sum = $cal_num1 + $cal_num2 + $cal_num3 + $cal_num4 + $cal_num5 + $cal_num6 + $cal_num7 + $cal_num8 + $cal_num9 + $cal_num10 + $cal_num11 + $cal_num12;

    //นำผลบวกมา modulation ด้วย 11 เพื่อหาเศษส่วน
    $cal_mod = $cal_sum % 11;
    //นำ 11 ลบ กับส่วนที่เหลือจากการ  modulation 
    $cal_2 = 11 - $cal_mod; 

    //ถ้าหากเลขที่ได้มา มีค่าเท่ากับเลขสุดท้ายของเลขบัตรประชาชน ถูกว่ามีความถูกต้อง
    if ($cal_2 == $num13) {
        return true;
    } 

    return false;
  
}

function update_cid_mmt_credit($parameter){

    $cid = $parameter['cid'];
    $access_token = $parameter['access_token'];
    $id_line_user = $parameter['id_line_user'];

    $curl = curl_init();

    curl_setopt_array($curl, array(
            CURLOPT_URL => $_ENV['MMT_CREDIT_API'].'/update-cid-mmt-credit',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'cid' => $cid,
                'id_line_user'=> $id_line_user
            ),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: '.$access_token
            ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $result = json_decode($response, true);
    return $result;
}

function get_mmt_credit($parameter){

    $cid = $parameter['cid'];
    $access_token = $parameter['access_token'];
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $_ENV['MMT_CREDIT_API'].'/get-mmt-credit',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'cid' => $cid
        ),
        CURLOPT_HTTPHEADER => array(
            'x-api-key: '.$access_token
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($response, true);
    return $result;

}

function middleware(){

    if(!isset($_COOKIE['token'])){
        return [
            'status' => 401,
            'message' => 'Unauthorized'
        ];
    }

    $jwt = $_COOKIE['token'];
    $result_jwt = checkJWT($jwt);

    $expires_in = $result_jwt->expires_in;
    $access_token = $result_jwt->access_token;
    $id_line_user = $result_jwt->id_line_user;

    $parameter = [
        'expires_in' => $expires_in,
        'access_token' => $access_token,
        'id_line_user' => $id_line_user
    ];

    $result_middleware = verify_route_post($parameter);

    if($result_middleware['status'] == 200) {
        return [
            'status' => 200,
            'data' => $result_jwt
        ];
    }

    return [
        'status' => 401,
        'message' => 'Bad Request',
        $result_jwt
    ];
    
}

/**
 * get path to assets folder
 * @param string $path path of file to assets folder
 * @return string full url path of file to assets folder
 */
function ASSET_URL($path){
    return $_ENV['APP_URL'].$_ENV['ASSET_URL'].$path;
}

/**
 * checkParameters isset and empty
 * @param array $parameters array of parameters
 * @return void
 */
function checkParameters($parameters) {
    foreach ($parameters as $parameter) {
        if (!isset($_POST[$parameter])) {
            echo json_encode([
                'status' => 400,
                'message' => "parameter $parameter is required"
            ]);
            exit;
        }

        if (empty($_POST[$parameter]) && $_POST[$parameter] !== '0') {
            echo json_encode([
                'status' => 400,
                'message' => "parameter $parameter can't be empty"
            ]);
            exit;
        }
    }
}

/**
 * generateColumns for DataTable SSR
 * @param array $dbColumns array of columns from database
 * @return array
 */
function generateColumns($dbColumns) {
    $columns = [];
    foreach ($dbColumns as $dbColumn => $value) {
        $columns[] = [
            'db' => $dbColumn,
            'dt' => $dbColumn,
        ];
    }
    return $columns;
}

/**
 * check is time in range
 * @param string $startTime start time in format H:i example "18:00"
 * @param string $endTime end time in format H:i example "05:00"
 * @return boolean "true if time is in range otherwise false"
 */
function isTimeInRange($startTime, $endTime) {
    $currentTime = date('H:i');
    if(($currentTime >= $startTime || $currentTime <= $endTime)){
    	return true;
	}
    return false;
}

function IncreaseResponseCount($id, $parameter = []){
    global $db_LINE;

    $webhook_id = null;
    $caption_id = null;

    if(isset($parameter['webhook_id']) && !empty($parameter['webhook_id'])){
        $webhook_id = $parameter['webhook_id'];
    }
    if(isset($parameter['caption_id']) && !empty($parameter['caption_id'])){
        $caption_id = $parameter['caption_id'];
    }

    $db_LINE->insert("INSERT INTO `line_bot_count` (`response_id`, `total`, `webhook_id`, `caption_id` ) VALUES (?, ?, ?, ?)", [$id, 1, $webhook_id, $caption_id]);

    $db_LINE->update("UPDATE `line_bot_response` SET `response_count` = `response_count` + ? WHERE `id` = ?", [1, $id]);

}

?>