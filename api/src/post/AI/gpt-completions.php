<?php 

function ChatGPT(){
    header('Content-type: application/json; charset=utf-8');

    if(!isset($_POST['content'])){
        return json_encode([
            'status' => 400,
            'message' => 'Bad Request'
        ]);

    }

    $content = $_POST['content'];

    if(empty($content)){
        return json_encode([
            'status' => 400,
            'message' => "parameter content is can't be empty"
        ]);

    }

    require_once __DIR__ . '/gpt-3.php';
    $result = GPTCompletions($content);
    return json_encode($result);
}

?>