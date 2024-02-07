<?php 

if($req_METHOD !== 'POST') {

    return ;
}

save_log("method post at ".time());

if($req_URL == $VERIFY_PATH){

    header('Content-type: application/json; charset=utf-8');

    if(!isset($_SERVER['HTTP_X_API_KEY'])){
        echo json_encode([
            'status' => 400,
            'message' => 'Bad Request'
        ]);
        exit;
    }

    $access_token = $_SERVER['HTTP_X_API_KEY'];
    $verify = verify_accessToken($access_token);

    if(!$verify){
        echo json_encode([
            'status' => 401,
            'message' => 'Unauthorized' 
        ]);
        exit;
    }

    echo json_encode([
        'status' => 200,
        'message' => 'OK',
        'result' => $verify
    ]);
    exit;
    
}

/* Middleware */
$result_middleware = middleware();

if($result_middleware['status'] != 200 ) {
    header('Content-type: application/json; charset=utf-8');
    echo json_encode([$result_middleware, 'message' => 'Unauthorized']);
    exit;
}

//SAVE CID REGISTER
if($req_URL == $SAVE_REGISTER){

    $result_jwt = $result_middleware['data']; //checkJWT($jwt);

    if(!$result_jwt){
        echo json_encode([
            'status' => 401,
            'message' => 'Unauthorized',
        ]);
        exit;
    }

    $id_line_user = $result_jwt->id_line_user;
    $access_token = $result_jwt->access_token;

    $cid = $_POST['cid']?$_POST['cid']:"";
    $fname = $_POST['fname']?$_POST['fname']:null;
    $lname = $_POST['lname']?$_POST['lname']:null;
    $tel = $_POST['tel']?$_POST['tel']:null;
    $address = $_POST['address']?$_POST['address']:null;
    $province = $_POST['province']?$_POST['province']:null;
    $zipcode = $_POST['zipcode']?$_POST['zipcode']:null;
    $email = $_POST['email']?$_POST['email']:null;

    //check empty
    if(empty($cid)){
        echo json_encode([
            'status' => 400,
            'message' => "โปรดใส่เลขบัตรประชาชน"
        ]);
        exit;
    }

    //check is number
    if(!is_numeric($cid)){
        echo json_encode([
            'status' => 400,
            'message' => "เลขบัตรประชาชนควรเป็นตัวเลข"
        ]);
        exit;
    }

    //check is more than 13 digit
    if(strlen($cid) > 13) {
        echo json_encode([
            'status' => 400,
            'message' => "เลขบัตรประชาชนควรมี 13 หลัก"
        ]);
        exit;
    }

    //verify cid
    $verify_cid = check_id_card($cid);
    if(!$verify_cid){
        echo json_encode([
            'status' => 400,
            'message' => "เลขบัตรประชาชนไม่ถูกต้อง"
        ]);
        exit;
    }

    $UserData = selectUserData($id_line_user);
    save_log("API save register UserData =>".json_encode($UserData) );

    if(count($UserData) <= 0) {
        echo json_encode([
            'status' => 404,
            'message' => 'Not Found, Please login again'
        ]);
        exit;
    }

    $update_parameter = [
        $cid, 
        $fname,
        $lname,
        $tel,
        $address,
        $province,
        $zipcode,
        $email,
        $id_line_user
    ];

    $result = $db_LINE->update("UPDATE `line_user` SET 
        `cid` = ?,
        `fname` = ?,
        `lname` = ?,
        `tel` = ?,
        `address` = ?,
        `province` = ?,
        `zipcode` = ?,
        `email` = ? 
    WHERE `line_user`.`id` = ?;", $update_parameter);

    $parameter = [
        'cid' => $cid,
        'id_line_user' => $id_line_user,  
        'access_token' => $access_token
    ];

    $result_update = update_cid_mmt_credit($parameter);
    save_log("Update register cid => ".$result);

    if(!$result){
        echo json_encode([
            'status' => 500,
            'message' => 'Something went wrong please try again'
        ]);
        exit;
    }

    /* set_cookie(['is_save_cid' => '1']); */

    echo json_encode([
        'status' => 200,
        'message' => 'OK'
    ]);
    exit;

}

if($req_URL == $LOGIN_PATH) {

    $result_jwt = $result_middleware['data'];

    $id_line_user = $result_jwt->id_line_user;
    $access_token = $result_jwt->access_token;
    $access_level = $result_jwt->access_level;
    $form_access_level = $_POST['access_level']?$_POST['access_level']:null;
    $form_fname = $_POST['form_fname']?$_POST['form_fname']:null;
    $form_lname = $_POST['form_lname']?$_POST['form_lname']:null;
    $verify_accessToken = verify_accessToken($access_token);

    if(!$verify_accessToken){
        echo json_encode([
            'status' => 401,
            'message' => 'Unauthorized' 
        ]);
        exit;
    }

    if($access_level < 100) {
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    if(!isset($form_access_level) || !isset($form_fname) || !isset($form_lname)){
        echo json_encode([
            'status' => 400,
            'message' => 'Bad Request required parameter access_level, fname, lname'
        ]);
        exit;
    }

    if(empty($form_access_level) || empty($form_fname) || empty($form_lname)){
        echo json_encode([
            'status' => 400,
            'message' => "parameter access_level, fname, lname is can't be empty"
        ]);
        exit;
    }

    //sql basic insert
    $sql = "INSERT INTO `line_user` (
        `access_level`,
        `fname`,
        `lname`,
        `added_by`
    ) VALUES (
        ?,
        ?,
        ?,
        ?
    );";

    $result_id= $db_LINE->insert($sql, [
        $form_access_level,
        $form_fname,
        $form_lname,
        $id_line_user
    ]);

    if(!$result_id){
        echo json_encode([
            'status' => 500,
            'message' => 'Something went wrong please try again'
        ]);
        exit;
    }

    $url = LineLoginUrl($result_id);

    save_log(" Generate URL => ".$url);

    echo json_encode([
        'status' => 200,
        'message' => 'OK',
        'url' => $url,
        'id' => $result_id
    ]);
    exit;

}

if($req_URL == '/line/delete') {

    $result_jwt = $result_middleware['data'];
    $id_line_user = $result_jwt->id_line_user;
    $access_token = $result_jwt->access_token;
    $access_level = $result_jwt->access_level;

    $id = $_POST['id']?$_POST['id']:null;

    if(!isset($id)){
        echo json_encode([
            'status' => 400,
            'message' => 'Bad Request required parameter id'
        ]);
        exit;
    }

    $verify_accessToken = verify_accessToken($access_token);

    if(!$verify_accessToken){
        echo json_encode([
            'status' => 401,
            'message' => 'Unauthorized' 
        ]);
        exit;
    }

    if($access_level < 10) {
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
        exit;
    }

    $sql = "DELETE FROM `line_user` WHERE `line_user`.`id` = ?;";
    $result = $db_LINE->delete($sql, [$id]);

    if(!$result){
        echo json_encode([
            'status' => 500,
            'message' => 'Something went wrong please try again'
        ]);
        exit;
    }

    echo json_encode([
        'status' => 200,
        'message' => 'OK'
    ]);
    exit;
}

?>