<?php 

function send_line_notify($token="", $body=[]){

    if(empty($token)){
        return false;
    }

    if(empty($body['message'])){
        return false;
    }
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://notify-api.line.me/api/notify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$token
            ),
    ));

    $response = curl_exec($curl);

    //get curl error
    $err = curl_error($curl);

    curl_close($curl);

    if($err){
        return $err;
    }

    return json_decode($response, true);

}


?>