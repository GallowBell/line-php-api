<?php 

if($req_METHOD !== 'POST') {

    return ;
}

$API_PUSH_MESSAGE = $_ENV['API_PUSH_MESSAGE'];
$LINE_ID_PATH = $_ENV['LINE_ID_PATH'];

$src = __DIR__ . '/src/post';

//endpoint PUSH MESSAGE
if($req_URL == $API_PUSH_MESSAGE) {
    require_once __DIR__ . '/src/post/api_push_message.php';
    api_push_message();
    exit;
}

//get line data
if($req_URL == $LINE_ID_PATH ) {
    require_once __DIR__ . '/src/post/line_id_path.php';
    line_id_path();
}

//get user data
if($req_URL == '/get-users' ) {

    header('Content-type: application/json; charset=utf-8');

    if($access_level < 100){
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    $result = $db_LINE->select("SELECT * FROM `line_user` WHERE access_level > 1 AND is_hide = 0 Order BY id ASC;");

    echo json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result,
    ]);
    exit;
}

//generate qr code for admin login
if($req_URL == '/qr-code-login' ) {

    if(!isset($_POST['url'])) {
        return ;
    }

    $url = $_POST['url'];
    $label = $_POST['label'];

    require_once __DIR__ . '/src/post/qr-code.php';

    $QRcode = QRcode([
        'data' => $url,
    ]);


    exit;

}

//edit line user
if($req_URL == '/edit-users' ) {

    header('Content-type: application/json; charset=utf-8');

    if($access_level < 100){
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    require_once __DIR__ . '/src/post/edit-users.php';
    editUsers();
    exit;
}

//check status login for created user
if($req_URL == '/check-login'){

    header('Content-type: application/json; charset=utf-8');

    if($access_level < 100){
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    require_once __DIR__ . '/src/post/check-login.php';
    CheckLogin();
    exit;
}




/* if($req_URL == '/delete-users'){

    header('Content-type: application/json; charset=utf-8');

    if($access_level < 100){
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    require_once __DIR__ . '/src/post/delete-user.php';
    deleteUser();
    exit;
} */

require_once $src . '/delete-user.php';
require_once $src . '/bot-manage/get-bot-response.php';
require_once $src . '/bot-manage/active-bot-response.php';
require_once $src . '/bot-manage/test-push-message.php';

$Routes->post('/delete-users', 'deleteUser', $verify );
$Routes->post('/get-bot-response', 'getBotResponse', $verify );
$Routes->post('/active-bot-response', 'activeBotResponse', $verify );
$Routes->post('/test-push-message', 'test_push_message', $verify );



if($req_URL == '/delete-bot-response'){
        
    header('Content-type: application/json; charset=utf-8');

    if($access_level < 10){
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    require_once __DIR__ . '/src/post/bot-manage/delete-bot-response.php';
    deleteBotResponse();
    exit;
}

if($req_URL == '/get-bot-caption') {
        
    header('Content-type: application/json; charset=utf-8');

    if($access_level < 10){
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    require_once __DIR__ . '/src/post/bot-manage/get-bot-caption.php';
    $result = getBotCaptionByResponseID();
    
    echo json_encode($result);
    exit;
}

if ($req_URL == '/edit-bot-response') {
        
    header('Content-type: application/json; charset=utf-8');

    if($access_level < 10){
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    require_once __DIR__ . '/src/post/bot-manage/edit-bot-response.php';
    editBotResponse();
    exit;
}

if($req_URL == '/add-bot-response') {
            
    header('Content-type: application/json; charset=utf-8');

    if($access_level < 10){
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    require_once __DIR__ . '/src/post/bot-manage/add-bot-response.php';
    addBotResponse();
    exit;

}

if($req_URL == '/get-demographics') {
                
    header('Content-type: application/json; charset=utf-8');

    if($access_level < 10){
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    require_once __DIR__ . '/../webhook/insight.php';
    
    $result = getFriendsDemographics();

    echo json_encode($result);
    exit;
}

if($req_URL == '/check-caption-duplicate') {
    header('Content-type: application/json; charset=utf-8');

    if($access_level < 10){
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    require_once __DIR__ . '/src/post/bot-manage/check-caption-duplicate.php';
    $result = CheckCaptionDuplicate();
    echo json_encode($result);
    exit;

}

if($req_URL == '/gpt-completions') {

    header('Content-type: application/json; charset=utf-8');

    if(!isset($_POST['content'])){
        echo json_encode([
            'status' => 400,
            'message' => 'Bad Request'
        ]);
        exit;
    }

    $content = $_POST['content'];

    if(empty($content)){
        echo json_encode([
            'status' => 400,
            'message' => "parameter content is can't be empty"
        ]);
        exit;
    }

    require_once __DIR__ . '/src/post/AI/gpt-3.php';
    $result = GPTCompletions($content);
    echo json_encode($result);
    exit;
}



?>