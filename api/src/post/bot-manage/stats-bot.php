<?php 

function CountTotalByResponseId($parameter=[]){
    global $db_LINE;


    $sql = "SELECT 
        COUNT(*) as totalResponse,
        LBC.response_id ,
        LBR.title
            
    FROM line_bot_count LBC

    LEFT JOIN line_bot_response LBR ON
    LBC.response_id = LBR.id ";
    $parameter = [];

    if(isset($parameter['date_start']) && isset($parameter['date_end'])){
        $sql .= "WHERE LBC.date_time BETWEEN ? AND ? ";
        $parameter[] = $parameter['date_start'];
        $parameter[] = $parameter['date_end'];
    }else{

        $sql .= "WHERE LBC.date_time BETWEEN ? AND ? ";
        //get date now
        $parameter[] = date('Y-m-01 00:00:00');
        //get first day of this month
        $parameter[] = date('Y-m-d 23:59:59');

    }

    $sql .= "GROUP BY LBC.response_id ORDER BY totalResponse DESC;";

    $result = $db_LINE->select($sql, $parameter);

    if(!$result){
        return [];
    }

    $result = array_map(function($item) {
        $item['totalResponse'] = (int) $item['totalResponse'];
        return $item;
    }, $result);

    return $result;
}

function CounTotalByCaptionId($parameter=[]){
    global $db_LINE;

    $sql = "SELECT
        COUNT(*) as totalCaption,
        LBC.caption_id,
        BC.caption
    FROM line_bot_count LBC 
    LEFT JOIN line_bot_caption BC ON 
    BC.id = LBC.caption_id 
    WHERE !isnull(caption_id) ";

    if(isset($parameter['date_start']) && isset($parameter['date_end'])){
        $sql .= " AND DATE(LBC.date_time) BETWEEN DATE(?) AND DATE(?) ";
        $parameter[] = $parameter['date_start'];
        $parameter[] = $parameter['date_end'];
    }else{

        $sql .= " AND DATE(LBC.date_time) BETWEEN DATE(?) AND DATE(?) ";
        //get date now
        $parameter[] = date('Y-m-01 00:00:00');
        //get first day of this month
        $parameter[] = date('Y-m-d 23:59:59');

    }

    $sql .= " GROUP BY caption_id  ORDER BY totalCaption DESC;";

    $result = $db_LINE->select($sql, $parameter);

    if(!$result){
        return [];
    }

    $result = array_map(function($item) {
        $item['totalCaption'] = (int) $item['totalCaption'];
        return $item;
    }, $result);

    return $result;
}


function getTotalResponseAsJSON($verify){

    header('Content-type: application/json; charset=utf-8');
    $access_level = $verify->access_level;
    if($access_level < 10){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }

    $parameter = [];

    if(isset($_POST['date_start']) && isset($_POST['date_end'])){
        $parameter['date_start'] = $_POST['date_start'];
        $parameter['date_end'] = $_POST['date_end'];
    }else{
        //get date now
        $parameter['date_start'] = date('Y-m-01 00:00:00');
        //get first day of this month
        $parameter['date_end'] = date('Y-m-d 00:00:00');

    }
    
    $result = CountTotalByResponseId($parameter);

    return json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result,
        'date_start' => $parameter['date_start'],
        'date_end' => $parameter['date_end']
    ]);

}

function getTotalCaptionAsJSON($verify){

    header('Content-type: application/json; charset=utf-8');
    $access_level = $verify->access_level;
    if($access_level < 10){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }

    $parameter = [];

    if(isset($_POST['date_start']) && isset($_POST['date_end'])){
        $parameter['date_start'] = $_POST['date_start'];
        $parameter['date_end'] = $_POST['date_end'];
    }else{
        //get date now
        $parameter['date_start'] = date('Y-m-01 00:00:00');
        //get first day of this month
        $parameter['date_end'] = date('Y-m-d 00:00:00');
    }
    
    $result = CounTotalByCaptionId();

    return json_encode([
        'status' => 200,
        'message' => 'OK',
        'data' => $result,
        'date_start' => $parameter['date_start'],
        'date_end' => $parameter['date_end']
    ]);

}


?>