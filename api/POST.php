<?php

if ($req_METHOD !== 'POST') {
    return;
}

$API_PUSH_MESSAGE = $_ENV['API_PUSH_MESSAGE'];
$LINE_ID_PATH = $_ENV['LINE_ID_PATH'];

/**
 * Path to /src/post
 * @var string $src
 */
$src = __DIR__ . '/src/post';

/** require file **/
require_once $src . '/api_push_message.php';
require_once $src . '/line_id_path.php';
require_once $src . '/get-users.php';
require_once $src . '/qr-code.php';
require_once $src . '/edit-users.php';
require_once $src . '/check-login.php';
require_once $src . '/delete-user.php';

/* Bot Manage */
require_once $src . '/bot-manage/get-bot-response.php';
require_once $src . '/bot-manage/active-bot-response.php';
require_once $src . '/bot-manage/test-push-message.php';
require_once $src . '/bot-manage/delete-bot-response.php';
require_once $src . '/bot-manage/get-bot-caption.php';
require_once $src . '/bot-manage/edit-bot-response.php';
require_once $src . '/bot-manage/add-bot-response.php';
require_once $src . '/bot-manage/check-caption-duplicate.php';
require_once $src . '/bot-manage/stats-bot.php';

/* AI */
require_once $src . '/AI/gpt-completions.php';

/* LINE Insight */
require_once $src . '/LINE-Insight/get-friend-demographics.php';
require_once $src . '/LINE-Insight/get-number-follower.php';
require_once $src . '/LINE-Insight/get-bot-info.php';
require_once $src . '/LINE-Insight/get-number-message-deliveries.php';
require_once $src . '/getNumberOfSentReplyMessagesRequest.php';
require_once $src . '/getMessageQuotaRequest.php';
require_once $src . '/message-detail/get-last-message-event.php';

/* LINE API */
require_once $src . '/LINE-API/index.php';

/** Define Routes **/
$Routes->post($API_PUSH_MESSAGE, 'api_push_message', $verify);
$Routes->post($LINE_ID_PATH, 'line_id_path', $verify);
$Routes->post('/get-users', 'get_users', $verify);
$Routes->post('/qr-code-login', 'qr_code_login', $verify);
$Routes->post('/edit-users', 'editUsers', $verify);
$Routes->post('/check-login', 'CheckLogin', $verify);
$Routes->post('/delete-users', 'deleteUser', $verify);

/* Bot Manage */
$Routes->post('/get-bot-response', 'getBotResponse', $verify);
$Routes->post('/active-bot-response', 'activeBotResponse', $verify);
$Routes->post('/test-push-message', 'test_push_message', $verify);
$Routes->post('/delete-bot-response', 'deleteBotResponse', $verify);
$Routes->post('/get-bot-caption', 'getBotCaptionByResponseID', $verify);
$Routes->post('/edit-bot-response', 'editBotResponse', $verify);
$Routes->post('/add-bot-response', 'addBotResponse', $verify);
$Routes->post('/check-caption-duplicate', 'CheckCaptionDuplicate', $verify);
$Routes->post('/count-by-response', 'getTotalResponseAsJSON', $verify);
$Routes->post('/count-by-caption', 'getTotalCaptionAsJSON', $verify);

/* AI */
$Routes->post('/gpt-completions', 'ChatGPT', $verify);

/* LINE Insight */
$Routes->post('/get-demographics', 'getFriendDemographics', $verify);
$Routes->post('/get-number-follower', 'getNumberFollowers', $verify);
$Routes->post('/get-bot-info', 'APIgetBotInfo');
$Routes->post('/get-number-message-deliveries', 'APIgetNumberOfMessageDeliveries', $verify);
$Routes->post('/get-number-sent-reply', 'APIgetNumberOfSentReplyMessagesRequest', $verify);
$Routes->post('/get-message-quota', 'APIgetMessageQuotaRequest', $verify);
$Routes->post('/get-last-message-event', 'APIgetLastMessageEvent', $verify);
$Routes->post('/get-last-message-event-by-userid', 'APIgetLastMessageEvent', $verify);

/* LINE API */
$Routes->post('/get-group-summary', 'ApigetGroupSummary', $_POST['groupId']?$_POST['groupId']:$_JSON['groupId']);
?>