<?php 

$URL_LOGIN_WEB = $APP_URL.$_ENV['URL_LOGIN_WEB'];
$SAVE_REGISTER = $APP_URL.$_ENV['SAVE_REGISTER'];
$REVOKE_PATH = $APP_URL.$_ENV['REVOKE_PATH'];

if(!isset($_COOKIE['token'])) {
    unset($_COOKIE['token']); 
    setcookie('token', '', -1, '/'); 
    header('Location: '. $URL_LOGIN_WEB);
    exit;
}

$jwt = $_COOKIE['token'];
$result_jwt = checkJWT($jwt);

if(!$result_jwt){
    unset($_COOKIE['token']); 
    setcookie('token', '', -1, '/'); 
    header('Location: '. $URL_LOGIN_WEB);
    exit;
}

$picture = $result_jwt->picture;
$access_token = $result_jwt->access_token;
$access_level = $result_jwt->access_level;
$name = $result_jwt->name;
$expires_in = $result_jwt->expires_in;
$id_line_user = $result_jwt->id_line_user;

$result_verify_accessToken = verify_accessToken( $access_token);

if(!$result_verify_accessToken) {
    remove_cookie(['token' => '' ]);
    header('Location: '. $URL_LOGIN_WEB);
    exit;
}

/**
 * User Data in array [0][parameter]
 * @var array $UserData
 */
$UserData = selectUserData($id_line_user);

if(count($UserData) == 0){
    remove_cookie(['token' => '' ]);
    header('Location: '. $URL_LOGIN_WEB);
    exit;
}

if($UserData[0]['is_active'] == 0){
    header('Location: /');
    exit;
}

//if access level more than 1 redirect to admin page
if($access_level <= 1) {
    header('Location: /');
    exit;
}


?>