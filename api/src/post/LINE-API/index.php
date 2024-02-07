<?php 

require_once __DIR__ . '/../../../../webhook/config.php';

function ApigetGroupSummary($groupId=''){

    $result = getGroupSummary($groupId);

    return json_encode([
        'res' => $result,
        'req' => [
            'req' => $_REQUEST,
            'post' => $_POST,
            'get' => $_GET,
        ],
    ]);

}
?>