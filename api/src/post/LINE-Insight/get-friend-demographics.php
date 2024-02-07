<?php 

function getFriendDemographics($verify) {
    global $db_LINE;

    header('Content-type: application/json; charset=utf-8');

    $access_level = $verify->access_level;

    if($access_level < 10){
        return json_encode([
            'status' => 403,
            'message' => 'Forbidden'
        ]);
    }


    $req_unixtime = time()-60;

    $data_demographic = $db_LINE->select("SELECT * FROM `line_demographic` WHERE `unix_timestamp` >= ? ORDER BY `id` DESC LIMIT 1;", [$req_unixtime]);

    save_log(json_encode($data_demographic));

    $total = count($data_demographic);

    if($total > 0) {
        return json_encode([
            'status' => 200,
            'message' => 'OK',
            'data' => $data_demographic[0]
        ]);
    }

    require_once __DIR__ . '/../../../../webhook/insight.php';
    
    $result = getFriendsDemographics();

    if($result['status'] == 200){

        $data = $result['data'];
        $available = $data['available'];
        $unixtime = time();
        $last_id_demograpic = $db_LINE->insert("INSERT INTO `line_demographic` ( `available`, `unix_timestamp` ) VALUES ( ?, ? );", [$available, $unixtime]);

        if($available){

            $ages = $data['ages'];
            $appTypes = $data['appTypes'];
            $areas = $data['areas'];
            $genders = $data['genders'];
            $subscriptionPeriods = $data['subscriptionPeriods'];

            //insert age
            foreach ($ages as $key => $value) {
                $percentage = $value['percentage'];
                $age = $value['age'];
                $db_LINE->insert("INSERT INTO `line_ages` (`percentage`, `age`, `line_demographic_id`) VALUES (?, ?, ?);", [$percentage, $age, $last_id_demograpic]);
            }

            //insert appType
            foreach ($appTypes as $key => $value) {
                $percentage = $value['percentage'];
                $appType = $value['appType'];
                $db_LINE->insert("INSERT INTO `line_appTypes` (`percentage`, `appType`, `line_demographic_id`) VALUES (?, ?, ?);", [$percentage, $appType, $last_id_demograpic]);
            }

            //insert areas
            foreach ($areas as $key => $value) {
                $percentage = $value['percentage'];
                $area = $value['area'];
                $db_LINE->insert("INSERT INTO `line_areas` (`percentage`, `area`, `line_demographic_id`) VALUES (?, ?, ?);", [$percentage, $area, $last_id_demograpic]);
            }

            //insert genders
            foreach ($genders as $key => $value) {
                $percentage = $value['percentage'];
                $gender = $value['gender'];
                $db_LINE->insert("INSERT INTO `line_genders` (`percentage`, `gender`, `line_demographic_id`) VALUES (?, ?, ?);", [$percentage, $gender, $last_id_demograpic]);
            }

            //insert appType
            foreach ($subscriptionPeriods as $key => $value) {
                $percentage = $value['percentage'];
                $subscriptionPeriod = $value['subscriptionPeriod'];
                $db_LINE->insert("INSERT INTO `line_subscriptionPeriods` (`percentage`, `subscriptionPeriod`, `line_demographic_id`) VALUES (?, ?, ?);", [$percentage, $subscriptionPeriod, $last_id_demograpic]);
            }

        }

    }

    return json_encode($result);
}


?>