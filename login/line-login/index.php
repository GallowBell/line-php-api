<?php 

require_once __DIR__ . '/config.php';

/**
 * REQUEST_URI
 * @var string $req_URL
 */
$req_URL = explode('?', $_SERVER['REQUEST_URI'])[0];

/**
 * REQUEST_METHOD
 * @var string $req_METHOD
 */
$req_METHOD = $_SERVER['REQUEST_METHOD'];

save_log("api line login ".time());
save_log("req_URL => ". $req_URL);
save_log("req_METHOD => ". $req_METHOD);

//get
require_once __DIR__ . '/GET.php';

//post
require_once __DIR__ . '/POST.php';

?>