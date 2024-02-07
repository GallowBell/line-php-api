<?php 
function getBotResponse($verify) {
    global $db_LINE;

    $access_level = $verify->access_level;

    require_once __DIR__ . '/../../../../class/SSR.php';

    $table = 'line_bot_response';
 
    // Table's primary key
    $primaryKey = 'id';
    
    // Array of database columns which should be read and sent back to DataTables.
    // The `db` parameter represents the column name in the database,
    // the `dt` parameter represents the DataTables column identifier. In this case object parameter names
    //using function to generateColumns

    $columns = [
        [ 
            'db' => 'id',
            'dt' => 'id',
            'formatter' => function( $d, $row ) {
                return (int)$d;
            }
        ],
        [
            'db' => "title",
            'dt' => 'title'
        ],
        [
            'db' => 'altText',
            'dt' => 'altText'
        ],
        [
            'db' => 'data_response',
            'dt' => 'data_response'
        ],
        [
            'db' => 'type',
            'dt' => 'type'
        ],
        [
            'db' => 'active',
            'dt' => 'active',
            'formatter' => function( $d, $row ) {
                if($d == 1){
                    return (bool)true;
                }
                return (bool)false;
            }
        ],
        [
            'db' => 'created',
            'dt' => 'created'
        ],
        [
            'db' => 'last_update',
            'dt' => 'last_update'
        ],
        [
            'db' => '(SELECT line_event_type.event_type FROM `line_bot_caption`, `line_event_type` WHERE line_bot_response.id = line_bot_caption.response_id AND line_event_type.id = line_bot_caption.event_type LIMIT 1)',
            'dt' => 'event_type'
        ],
        //for count from table log line_bot_count
        /* [
            'db' => "(SELECT COUNT(total) FROM `line_bot_count` WHERE line_bot_response.id = line_bot_count.response_id)",
            'dt' => 'response_count',
            'formatter' => function( $d, $row ) {
                return (int)$d;
            }
        ], */
        [
            'db' => "response_count",
            'dt' => 'response_count',
            'formatter' => function( $d, $row ) {
                return (int)$d;
            }
        ],
        [
            'db' => 'is_use_ai',
            'dt' => 'is_use_ai',
            'formatter' => function( $d, $row ) {
                if($d == 1){
                    return (bool)true;
                }
                return (bool)false;
            }
        ],
        [
            'db' => 'is_use_time',
            'dt' => 'is_use_time',
            'formatter' => function( $d, $row ) {
                if($d == 1){
                    return (bool)true;
                }
                return (bool)false;
            }
        ],
        [
            'db' => 'notificationDisabled',
            'dt' => 'notificationDisabled',
            'formatter' => function( $d, $row ) {
                if($d == 1){
                    return (bool)true;
                }
                return (bool)false;
            }
        ],
        [
            'db' => 'start_time',
            'dt' => 'start_time'
        ],
        [
            'db' => 'end_time',
            'dt' => 'end_time'
        ],
    ];

    $where = " `is_hide` = 0 ";
    
    // SQL server connection information
    $PDO_Connection = $db_LINE->getPDO();

    $joinQuery = $table;

    header('Content-type: application/json; charset=utf-8');

    if($access_level < 10){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden',
        ]);
    }
    
    $result = SSP::complex( $_POST, $PDO_Connection, $joinQuery, $primaryKey, $columns, null , $where );

    return json_encode($result);

}
?>