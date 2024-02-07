<?php 

use \LINE\Clients\MessagingApi\Model\TextMessage;
use \LINE\Clients\MessagingApi\Model\FlexMessage;
use LINE\Constants\MessageType;

function CheckCredit($parameter = []) {
    global  $db_LINE;

    $userId = $parameter['userId'];

    save_log("CheckCredit userId => ". $userId);

    $result = $db_LINE->select("SELECT *, LU.id as id, LL.id as login_id FROM `line_user` LU INNER JOIN `line_login` LL ON LL.id = LU.id_line_login WHERE LU.userId = ?; ", [$userId]);
    $profile = $result[0];

    save_log("CheckCredit profile => ". json_encode($profile));

    $parameter = [
      'cid' => $profile['cid'],
      'access_token' => $profile['access_token']
    ];

    //$data_credit = get_mmt_credit($parameter);

    $data_credit = getDataCredit($profile);

    save_log("CheckCredit data => ". json_encode($data_credit) );

    $total = count($data_credit);

    if($total == 0) {
        //$result = '{"type": "text","contents": ["ไม่พบข้อมูล"]}';
        /* $result = [
          'type' => 'text',
          'text' => 'ไม่พบข้อมูล'
        ]; */

        $message[] = new TextMessage([
            'type' => MessageType::TEXT,
            'text' => 'ไม่พบข้อมูลโปรดผูกบัตรก่อนที่เมนูผูกบัตร'
        ]);

        //save_log("CheckCredit result => ". json_decode($result, true) );
        save_log("CheckCredit result => ". $message );

        return $message;
    }

    $contents = '';
    for ($i=0; $i < $total ; $i++) { 

      $data = $data_credit[$i];
      $tel = $data['tel'];

      //mask $tel_mask by 123-xxx-xx90
      $tel_mask = substr($tel, 0, 3) . "-xxx-xx" . substr($tel, -2);

      $fname = $data['fname'];
      $lname = $data['lname'];
      $member_no = $data['member_no'];
      $remainingCredits = number_format($data['remainingCredits'], 2, '.', ',');
      $level = $data['level'];
      $date_TH = $data['expire_date'];

      $date_TH = date("d/m/Y", strtotime($date_TH));

      $URL_IMG = $_ENV['IMG_CREDIT_CARD'];

      $img_level = [
          [
            "level" => 1,
            "url" => $URL_IMG.'/silver2.png',
            "text_color" => "#2d2d2d",
            "level_txt" => "Silver"
          ],
          [
            "level" => 2,
            "url" => $URL_IMG.'/gold2.png',
            "text_color" => "#fafec1",
            "level_txt" => "Gold"
          ],
          [
            "level" => 3,
            "url" => $URL_IMG.'/platinum2.png',
            "text_color" => "#cdcdcd",
            "level_txt" => "Platinum"
          ],
          [
            "level" => 4,
            "url" => $URL_IMG.'/emerald.png',
            "text_color" => "#d6bc5f",
            "level_txt" => "Emerald"
          ],
          [
            "level" => 5,
            "url" => $URL_IMG.'/diamond2.png',
            "text_color" => "#9e7023",
            "level_txt" => "Diamond"
          ],
      ];

      foreach ($img_level as $key => $value) {
          if($value['level'] == $level){
              $img_data = $img_level[$key];
          }
      }

      save_log("CheckCredit img_data => ". json_encode($img_data) );

      $contents .= '{
          "type": "bubble",
          "size": "giga",
          "direction": "ltr",
          "body": {
            "type": "box",
            "layout": "vertical",
            "contents": [
              {
                "type": "image",
                "url": "'.$img_data['url'].'",
                "position": "absolute",
                "size": "full",
                "aspectRatio": "2:1.34",
                "aspectMode": "cover",
                "gravity": "top",
                "align": "start",
                "margin": "none",
                "offsetStart": "none",
                "offsetBottom": "none"
              },
              {
                "type": "text",
                "text": "ID '.$member_no.'",
                "weight": "bold",
                "style": "normal",
                "wrap": false,
                "gravity": "top",
                "align": "end",
                "position": "absolute",
                "color": "'.$img_data['text_color'].'",
                "size": "16px",
                "offsetTop": "22%",
                "offsetEnd": "10px",
                "offsetBottom": "0px",
                "offsetStart": "0px",
                "contents": [],
                "scaling": false,
                "margin": "none"
              },
              {
                "type": "box",
                "layout": "horizontal",
                "contents": [
                  {
                    "type": "box",
                    "layout": "baseline",
                    "contents": [],
                    "position": "absolute",
                    "offsetEnd": "10px"
                  }
                ],
                "position": "absolute",
                "width": "100%",
                "offsetTop": "-10%",
                "offsetBottom": "0px",
                "offsetStart": "0px",
                "offsetEnd": "10px",
                "justifyContent": "flex-end",
                "alignItems": "center"
              },
              {
                "type": "box",
                "layout": "horizontal",
                "contents": [
                  {
                    "type": "box",
                    "layout": "baseline",
                    "contents": [
                      {
                        "type": "text",
                        "text": "'.$img_data['level_txt'].' Member ",
                        "color": "'.$img_data['text_color'].'",
                        "weight": "bold",
                        "style": "normal",
                        "align": "start",
                        "scaling": true,
                        "wrap": false,
                        "offsetTop": "0%",
                        "gravity": "top",
                        "position": "absolute"
                      }
                    ],
                    "justifyContent": "flex-start",
                    "alignItems": "center",
                    "spacing": "sm",
                    "margin": "sm",
                    "paddingAll": "5%",
                    "offsetTop": "87%",
                    "position": "absolute",
                    "width": "90%",
                    "offsetStart": "5%"
                  },
                  {
                    "type": "box",
                    "layout": "baseline",
                    "contents": [
                      {
                        "type": "text",
                        "text": "EXP. (ว/ด/ป) '.$date_TH.'",
                        "color": "'.$img_data['text_color'].'",
                        "weight": "regular",
                        "style": "normal",
                        "align": "start",
                        "scaling": true,
                        "wrap": false,
                        "offsetTop": "0%",
                        "gravity": "top",
                        "position": "absolute",
                        "size": "xxs"
                      }
                    ],
                    "justifyContent": "flex-start",
                    "alignItems": "center",
                    "spacing": "sm",
                    "margin": "sm",
                    "paddingAll": "5%",
                    "offsetTop": "98%",
                    "position": "absolute",
                    "width": "90%",
                    "offsetStart": "5%"
                  }
                ],
                "position": "absolute",
                "width": "100%",
                "alignItems": "center",
                "offsetTop": "0%",
                "offsetBottom": "0px",
                "offsetStart": "0px",
                "offsetEnd": "0px",
                "spacing": "none",
                "justifyContent": "flex-start",
                "paddingAll": "10px"
              },
              {
                "type": "box",
                "layout": "vertical",
                "contents": [],
                "position": "relative",
                "paddingTop": "63%"
              },
              {
                "type": "box",
                "layout": "horizontal",
                "contents": [
                  {
                    "type": "box",
                    "layout": "baseline",
                    "contents": [
                      {
                        "type": "text",
                        "text": "คุณ '.$fname.' '.$lname. '",
                        "size": "27px",
                        "margin": "none",
                        "color": "'.$img_data['text_color'].'",
                        "align": "center",
                        "gravity": "center",
                        "wrap": false,
                        "scaling": true,
                        "weight": "bold",
                        "offsetTop": "10px"
                      }
                    ],
                    "paddingAll": "10px",
                    "offsetTop": "5%"
                  }
                ],
                "position": "absolute",
                "alignItems": "center",
                "width": "100%",
                "height": "100%",
                "justifyContent": "center",
                "offsetTop": "0%"
              }
            ],
            "backgroundColor": "#00000000",
            "position": "relative",
            "offsetBottom": "0%",
            "offsetTop": "0%",
            "spacing": "none",
            "justifyContent": "center",
            "borderWidth": "none",
            "cornerRadius": "none",
            "margin": "none",
            "offsetStart": "0%",
            "offsetEnd": "0%",
            "alignItems": "center",
            "paddingTop": "0%",
            "paddingBottom": "0%",
            "paddingStart": "0%",
            "paddingEnd": "0%"
          },
          "footer": {
            "type": "box",
            "layout": "vertical",
            "contents": [
              
              {
                "type": "box",
                "layout": "horizontal",
                "contents": [
                  {
                    "type": "box",
                    "layout": "baseline",
                    "contents": [
                      {
                        "type": "text",
                        "text": "จำนวนเครดิต"
                      },
                      {
                        "type": "text",
                        "text": "'.$remainingCredits.'"
                      }
                    ],
                    "spacing": "sm",
                    "margin": "sm"
                  }
                ]
              },
              {
                "type": "box",
                "layout": "horizontal",
                "contents": [
                  {
                    "type": "box",
                    "layout": "baseline",
                    "contents": [
                      {
                        "type": "text",
                        "text": "เบอร์โทรศัพท์"
                      },
                      {
                        "type": "text",
                        "text": "'.$tel_mask.'"
                      }
                    ],
                    "spacing": "sm",
                    "margin": "sm"
                  }
                ]
              }
            ],
            "offsetTop": "none",
            "paddingTop": "xl",
            "paddingBottom": "xl"
          },
          "styles": {
            "header": {
              "separator": false,
              "backgroundColor": "#00000000"
            },
            "hero": {
              "backgroundColor": "#00000000",
              "separator": false
            },
            "body": {
              "backgroundColor": "#00000000",
              "separator": false
            },
            "footer": {
              "backgroundColor": "#00000000",
              "separator": false
            }
          }
        }';

        if($contents != '' && $i != $total-1){
            $contents .= ',';
        }

        save_log("CheckCredit contents => ". $contents );

    }//end loop for

    $result = '{
      "type": "flex",
      "contents": ['.$contents.'],
      "altText": "ข้อมูลสมาชิก"
    }';

    save_log("CheckCredit flex => ". json_decode($result, true) );

    $data = json_decode($result, true);

    $type = $data['type'];
    $contents = $data['contents'];
    $total_contents = count($contents);

    if($type === 'text'){
        for ($i=0; $i < $total_contents; $i++) { 
            $content = $contents[$i];
            $message[] = new TextMessage([
                'type' => MessageType::TEXT,
                'text' => $content
            ]);
        }            
    }

    if($type === 'flex') {
        //convert json to array
        $altText = $data['altText']?$data['altText']:"ข้อความอัตโนมัติ";
        for ($i=0; $i < $total_contents; $i++) { 
            $content = $contents[$i];
            $message[] = new FlexMessage([
                'type' => MessageType::FLEX,
                'altText' => $altText,
                'contents' => $content
            ]);
        }
    }

    return $message;
}
?>