<?php 

function GPTCompletions($content = "", $parameter=[]) {
    global $db_LINE;

    if($content == "") {
        return [
            'status' => 400,
            'message' => 'Bad Request'
        ];
    }

    if(isset($parameter['message_id'])) {
        $message_id = $parameter['message_id'];
    } else {
        $message_id = null;
    }

    $gpt_config = $db_LINE->query("SELECT * FROM `gpt_config`")[0];

    save_log(' gpt_config => '. json_encode($gpt_config));

    $OPENAI_API_URL = $_ENV['OPENAI_API_URL'];
    $OPENAI_API_KEY = $_ENV['OPENAI_API_KEY'];
    $OPENAI_API_KEY_TYPE = $_ENV['OPENAI_API_KEY_TYPE'];

    $curl = curl_init();

    $settings = [
        "model" => $gpt_config['model'],
        "messages" => [
            [
                "role" => "system",
                "content" => $gpt_config['system_content']
            ],
            [
                "role" => "user", 
                "content" => $content
            ]
        ],
        "temperature" => (double)$gpt_config['temperature']
    ];

    curl_setopt_array($curl, array(
        CURLOPT_URL => $OPENAI_API_URL.'/v1/chat/completions',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($settings),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: ' . $OPENAI_API_KEY_TYPE . ' '.$OPENAI_API_KEY
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $lastId = SaveResponseGPT($response, $message_id);

    $result = json_decode($response, true);
        
    return $result;
}

function SaveResponseGPT($response='', $message_id){
    global $db_LINE;
    $id = $db_LINE->insert("INSERT INTO `chat_gpt_response` (`response`, `line_message_id`) VALUES (?, ?)", [$response, $message_id]);
    return $id;
}

?>