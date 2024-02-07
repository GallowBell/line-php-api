<?php 

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TooManyRedirectsException;


$APP_URL = $_ENV['APP_URL'];
$TOKEN_PATH = $_ENV['TOKEN_PATH'];
$LOGIN_PATH = $_ENV['LINE_LOGIN_PATH'];
$VERIFY_PATH = $_ENV['VERIFY_PATH'];
$URL_REGISTER_CID = $APP_URL.$_ENV['URL_REGISTER_CID'];
$URL_LOGIN_WEB = $APP_URL.$_ENV['URL_LOGIN_WEB'];
$SAVE_REGISTER = $_ENV['SAVE_REGISTER'];
$REVOKE_PATH = $_ENV['REVOKE_PATH'];

function LineLoginToken($code){

    $client = new Client([
        'headers' => [ 'Content-Type' => 'application/x-www-form-urlencoded' ]
    ]);

    $client_id = $_ENV['LINE_LOGIN_CHANNEL_ID'];
    $client_secret = $_ENV['LINE_LOGIN_CHANNEL_SECRET'];

    /* 
    example return 
    {
        "access_token": "bNl4YEFPI/hjFWhTqexp4MuEw5YPs...",
        "expires_in": 2592000,
        "id_token": "eyJhbGciOiJIUzI1NiJ9...",
        "refresh_token": "Aa1FdeggRhTnPNNpxr8p",
        "scope": "profile",
        "token_type": "Bearer"
    }
    */

    try {
        $redirect_uri = $_ENV['LINE_LOGIN_REDIRECT'];
        $response = $client->post($_ENV['LINE_API_URL'].'/oauth2/v2.1/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirect_uri,
                'client_id' => $client_id,
                'client_secret' => $client_secret
            ]
        ]);
        
        $body = $response->getBody();
    
        save_log("LineLoginToken body => ". $body);
    
        $result = json_decode($body, true);
        return $result;
    } catch (ClientException $e) {
        $error = "LineLoginToken => Client error: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (ServerException $e) {
        $error = "LineLoginToken => Server error: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (ConnectException $e) {
        $error = "LineLoginToken => Connection error: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (TooManyRedirectsException $e) {
        $error = "LineLoginToken => Too many redirects: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (RequestException $e) {
        $error = "LineLoginToken => Request error: " . $e->getMessage();
        save_log($error);
        return false;
    }

}

//https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=1234567890&redirect_uri=https%3A%2F%2Fexample.com%2Fauth%3Fkey%3Dvalue&state=12345abcde&scope=profile%20openid&nonce=09876xyz


//function generate url line login
function LineLoginUrl($added_line_user = null){

    $client_id = $_ENV['LINE_LOGIN_CHANNEL_ID'];
    $redirect_uri = urlencode($_ENV['LINE_LOGIN_REDIRECT']);
    $jwt = generateState();
    $state = $jwt[1];
    $scope = $_ENV['LINE_LOGIN_SCOPE'];
    $nonce = generate_uuid();

    $save_data = [
        'nonce' => $nonce,
        'jwt' => $jwt,
        'added_line_user' => $added_line_user
    ];

    $id = save_login_data($save_data);

    save_log("LineLoginUrl id => ". $id);

    $url = "https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=". $client_id ."&redirect_uri=". $redirect_uri ."&state=". $state ."&scope=". $scope ."&nonce=". $nonce . "&id=". $id;

    return $url;
}

function generateState(){

    $time = time()*1000;
    $payload = [
        'iat' => $time,
        'exp' => $time + 300,
        "iss" => $_ENV['APP_URL']?$_ENV['APP_URL']:"APP_URL"
    ];

    $jwt = encodeJWT($payload);

    //get only jwt body
    $jwt = explode(".", $jwt);
    return $jwt;
}

function save_login_data($parameter){
    global $db_LINE;

    $header = $parameter['jwt'][0];
    $body = $parameter['jwt'][1];
    $footer = $parameter['jwt'][2];
    $nonce = $parameter['nonce'];
    $added_line_user = $parameter['added_line_user'];
    $session_id = session_id();
    $ip = get_client_ip();

    $id = $db_LINE->insert("INSERT INTO `line_login_token` ( `header`, `body`, `footer`, `session`, `ip`, `nonce`, `added_line_user`) VALUES ( ?, ?, ?, ?, ?, ?, ?);", [$header, $body, $footer, $session_id, $ip, $nonce, $added_line_user]);
    return $id;
}

function verify_state($state){
    global $db_LINE;

    $result = $db_LINE->select("SELECT * FROM `line_login_token` WHERE `body` = ?", [ $state ]);
    
    if(count($result) == 0){
        return false;   
    }

    return $result;
    
}



function verify_ID_Token($id_token){
    $client = new Client([
        'headers' => [ 'Content-Type' => 'application/x-www-form-urlencoded' ]
    ]);
    
    $response = $client->post($_ENV['LINE_API_URL'].'/oauth2/v2.1/verify', [
        'form_params' => [
            'id_token' => $id_token,
            'client_id' => $_ENV['LINE_LOGIN_CHANNEL_ID']
        ]
    ]);
    
    $body = $response->getBody();

    save_log("verify_ID_Token body => ". $body);

    $result = json_decode($body, true);

    /* //example response

    {
        "iss": "https://access.line.me",
        "sub": "U63ddba9d7cad1f85d4f06c312743e452",
        "aud": "2001676971",
        "exp": 1700039235,
        "iat": 1700035635,
        "nonce": "6452e016-4591-43b9-a26c-e55370c3f628",
        "amr": [
            "linesso"
        ],
        "name": "Prapon",
        "picture": "https://profile.line-scdn.net/0m0440fdef725163b02a5fc5862b7257116fbc783147d7"
    } */

    return $result;
}

function save_line_user_token($parameter){
    global $db_LINE;

    $iss = $parameter["iss"];
    $userId = $parameter["sub"];
    $aud = $parameter["aud"];
    $exp = $parameter["exp"];
    $iat = $parameter["iat"];
    $nonce = $parameter["nonce"];
    $amr = $parameter["amr"];
    $displayName = $parameter["name"];
    $pictureUrl = $parameter["picture"];
    $id_line_login = $parameter["id_line_login"]?$parameter["id_line_login"]:null;
    $added_line_user = $parameter["added_line_user"]?$parameter["added_line_user"]:null;

    save_log("save_line_user_token parameter => ". json_encode($parameter));

    save_log('added_line_user => '. $added_line_user);
    $is_admin = false;
    if(!empty($added_line_user)){
        $select = $db_LINE->select("SELECT * FROM `line_user` WHERE `id` = ?", [$added_line_user]);
        $is_admin = true;
    }else{
        $select = $db_LINE->select("SELECT * FROM `line_user` WHERE `userId` = ?", [$userId]);
    }

    save_log('select => '.json_encode($select));
    //$select = $db_LINE->select("SELECT * FROM `line_user` WHERE `userId` = ?", [$userId]);

    if(count($select) > 0){
        if($is_admin) {
            $result = $db_LINE->update("UPDATE line_user SET 
                userId = ?,
                displayName = ?,
                pictureUrl = ?,
                id_line_login = ?
            WHERE id = ?;", [
                    $userId,
                    $displayName,
                    $pictureUrl,
                    $id_line_login,
                    $added_line_user
            ]);
            return $select[0]['id'];
        }

        $result = $db_LINE->update("UPDATE line_user SET 
            userId = ?,
            displayName = ?,
            pictureUrl = ?,
            id_line_login = ?
        WHERE userId = ?;", [
                $userId,
                $displayName,
                $pictureUrl,
                $id_line_login,
                $userId
        ]);
        return $select[0]['id'];
    }

    $result = $db_LINE->insert("INSERT INTO line_user (userId, displayName, pictureUrl, id_line_login) VALUES (?, ?, ?, ?);", [
        $userId,
        $displayName,
        $pictureUrl,
        $id_line_login
    ]);

    if(!$result){
        return false;
    }

    $last_id = $db_LINE->getPDO()->lastInsertId();

    return $last_id;

}

function save_line_login($parameter){
    global $db_LINE;

    $access_token = $parameter["access_token"];
    $expires_in = $parameter["expires_in"]+time();
    $id_token = $parameter["id_token"];
    $refresh_token = $parameter["refresh_token"];
    $scope = $parameter["scope"];
    $token_type = $parameter["token_type"];
    $userId = $parameter["userId"];
    $line_login_token_id = $parameter["line_login_token_id"];

    $id = $db_LINE->insert("INSERT INTO `line_login` ( 
                                                        `access_token`,
                                                        `expires_in`,
                                                        `id_token`,
                                                        `refresh_token`,
                                                        `scope`,
                                                        `token_type`,
                                                        `userId`,
                                                        `line_login_token_id`
                                                    ) VALUES (
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?
                                                    );", [
        $access_token,
        $expires_in,
        $id_token,
        $refresh_token,
        $scope,
        $token_type,
        $userId,
        $line_login_token_id
    ]);

    return $id;

}

function RevokeAccessToken($parameter){

    $access_token = $parameter["access_token"];
    $client_id = $_ENV['LINE_LOGIN_CHANNEL_ID'];
    $client_secret = $_ENV['LINE_LOGIN_CHANNEL_SECRET'];

    $client = new Client([
        'headers' => [ 'Content-Type' => 'application/x-www-form-urlencoded' ]
    ]);

    try {
        $response = $client->post($_ENV['LINE_API_URL'].'/oauth2/v2.1/revoke', [
            'form_params' => [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'access_token' => $access_token
            ]
        ]);

        $status = $response->getStatusCode();
        save_log("status revoke => ". $status);

        if($status != 200){
            return false;
        }
    
        return true;

    }catch (ClientException $e) {
        $error = "LineLoginToken => Client error: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (ServerException $e) {
        $error = "LineLoginToken => Server error: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (ConnectException $e) {
        $error = "LineLoginToken => Connection error: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (TooManyRedirectsException $e) {
        $error = "LineLoginToken => Too many redirects: " . $e->getMessage();
        save_log($error);
        return false;
    } catch (RequestException $e) {
        $error = "LineLoginToken => Request error: " . $e->getMessage();
        save_log($error);
        return false;
    }


}

function DeleteLineLogin($parameter){
    global $db_LINE;

    if(empty($parameter["id_line_user"])){
        return false;
    }

    $id_line_user = $parameter["id_line_user"];

    $select = $db_LINE->select("SELECT id_line_login FROM `line_user` WHERE `id` = ?", [$id_line_user])[0]['id_line_login'];

    save_log('id_line_login => ' .$select);

    $result = $db_LINE->delete("DELETE FROM line_login WHERE `line_login`.`id` = ?", [$select]);

    if(!$result){
        return false;
    }

    return true;

}



?>