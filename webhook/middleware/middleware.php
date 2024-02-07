<?php 

//function get header
function getHeader($name = ''){
    $headers = getallheaders();

    if($name == ''){
        return $headers;
    }

    return $headers[$name];

}

//function check x-line-signature in header
function verify_signature($parameter){

    $channelSecret = $_ENV['LINE_BOT_CHANNEL_SECRET']; // Channel secret string
    $httpRequestBody = $parameter['body']; // Request body string
    $signature_header = $parameter['header']['X-Line-Signature']; // Request header string

    $hash = hash_hmac('sha256', $httpRequestBody, $channelSecret, true);
    $signature = base64_encode($hash);

    save_log("signature_encode " . $signature);
    save_log("signature_header " . $signature_header);

    if($signature ===  $signature_header) {
        return true;
    }else{
        return false;
    }

}

//function save header
function save_header(){
    global $db_LINE;

    $HEADER = getallheaders();
    $user_agent = $HEADER['User-Agent']?$HEADER['User-Agent']:"";
    $line_signature = $HEADER['X-Line-Signature']?$HEADER['X-Line-Signature']:"";
    $content_type = $HEADER['Content-Type']?$HEADER['Content-Type']:"";
    $content_length = $HEADER['Content-Length']?$HEADER['Content-Length']:"";
    $forwarded_for = $HEADER['x-forwarded-for']?$HEADER['x-forwarded-for']:"";

    $parameter = [
        $user_agent,
        $line_signature,
        $content_type,
        $content_length,
        $forwarded_for,
    ];


    $sql = "INSERT INTO `line_req_header`(`user_agent`, `line_signature`, `content_type`, `content_length`, `forwarded_for`) VALUES (?, ?, ?, ?, ?)";
    $result = $db_LINE->insert($sql, $parameter);

    return $result;

    //return 1;
}




?>