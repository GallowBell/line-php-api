<?php 

//method post only
if($req_METHOD !== 'GET') {
    return;
}

if($req_URL == $VERIFY_PATH){

    if(empty($_COOKIE['token']) ) {
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

    $expires_in = $result_jwt->exp;
    $access_token = $result_jwt->access_token;

    $verify = verify_accessToken($access_token);

    if(!$verify){
        header('Location: '. $URL_LOGIN_WEB);
        exit;
    }

    $id_line_user = $result_jwt->id_line_user;
    $UserData = selectUserData($id_line_user);

    save_log("UserData => ". json_encode($UserData));

    if($expires_in < time()) {

        $refresh_token = $UserData[0]['refresh_token'];
        $token_refresh = refreshToken($refresh_token);
        $token_refresh['id'] = $id_line_user;
        update_line_login($token_refresh);
        $expires_in = $token_refresh['expires_in'] + time();

        save_log("token_refresh => ". json_encode($token_refresh) );

        $db_LINE->disconnect();

        if(empty($UserData[0]['cid'])){
            header('Location: '. $URL_REGISTER_CID);
            exit;
        }
       
    }

    $payload = [
        'id_line_user' => $id_line_user,
        'access_token' => $token_refresh['access_token'],
        'access_level' => $userData[0]['access_level'],
        'expires_in' => $expires_in,
        'name' => $UserData[0]['displayName'],
        'picture' => $UserData[0]['pictureUrl'],
    ];
    $jwt = encodeJWT($payload);

    $set_cookie = [
       'token' => $jwt,
    ];

    set_cookie($set_cookie);

    header('Location: '. $APP_URL);
    exit;

}


if($req_URL == $LOGIN_PATH) {

    if(!empty($_COOKIE['token'])) {
        header('Location: '. $VERIFY_PATH);
        exit;
    }

    $url = LineLoginUrl();

    
    header('Location: '. $url);
    exit;
}

if($req_URL == $TOKEN_PATH) {

    save_log(" Request list ". json_encode($_GET));

    $code = $_GET['code'];
    $state = $_GET['state'];

    $verify = verify_state($state)[0];
 
    save_log(" Request verify ". json_encode($verify));

    $added_line_user = $verify['added_line_user']?$verify['added_line_user']:null;

    if(!$verify){
        header('Location: '. $URL_LOGIN_WEB);
        exit;
    }

    $token = LineLoginToken($code);

    if(!$token){
        header('Location: '. $URL_LOGIN_WEB);
        exit;
    }

    save_log(" Request token ". json_encode($token));

    $id_token = $token['id_token'];
    $access_token = $token['access_token'];
    $data = verify_ID_Token($id_token);

    if($data['nonce'] !== $verify['nonce']) {
        header('Location: '. $URL_LOGIN_WEB);
        exit;
    }

    $verify_accessToken = verify_accessToken($access_token);

    if(!$verify){
        header('Location: '. $URL_LOGIN_WEB);
        exit;
    }

    $token['userId'] = $data['sub'];
    $token['line_login_token_id'] = $verify['id'];

    $id = save_line_login($token);
    $data['id_line_login'] = $id;

    $data['added_line_user'] = $added_line_user;

    save_log(' data => '. json_encode($data));

    save_log(" Request get data ". json_encode($_REQUEST));

    $id_line_user = save_line_user_token($data);

    save_log("id_line_user => ". $id_line_user);

    $userData = selectUserData($id_line_user);

    if($userData[0]['is_active'] == 0){
        $userData[0]['access_level'] = 1;
    }

    $payload = [
        'id_line_user' => $id_line_user,
        'access_token' => $access_token,
        'access_level' => $userData[0]['access_level'],
        'expires_in' => time() + $token['expires_in'],
        'name' => $data['name'],
        'picture' => $data['picture'],
    ];

    save_log("payload => ". json_encode($payload));

    $jwt = encodeJWT($payload);

    $cookie = [
        'token' => $jwt,
    ];

    set_cookie($cookie);

    $url = $URL_REGISTER_CID;

    header('Location: '. $url);
    exit;
}


if($req_URL == $REVOKE_PATH) {

    $result_middleware = middleware();

    remove_cookie(['token' => '' ]);

    if($result_middleware['status'] != 200 ) {
        header('Location: '. $URL_LOGIN_WEB);
        exit;
    }

    $result_jwt = $result_middleware['data']; 

    if(!$result_jwt){
        header('Location: '. $URL_LOGIN_WEB);
        exit;
    }

    $id_line_user = $result_jwt->id_line_user;
    $access_token = $result_jwt->access_token;

    $parameter = [
        'access_token' => $access_token,
        'id_line_user' => $id_line_user,
    ];

    $revoke_result = RevokeAccessToken($parameter);
    $delete_result = DeleteLineLogin($parameter);

    save_log("revoke_result => ". $revoke_result);
    save_log("delete_result => ". $delete_result);

    header('Location: '. $URL_LOGIN_WEB);
    exit;

}


?>