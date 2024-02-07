<?php 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;

$privateKey = <<<EOD
{
  "alg": "RS256",
  "d": "PtLOhXBzp1vIv3tqq5ZBivDYQ13F85PhoYdtA-LI92L1Q0jyfcHk2UDu_pB2SDZF4EkhiUxaVhhO8g7Hl3VMBQn_VMIYp-4SgiS1hFgSwCT_s81OxyUcvj4LtCC8I2R47FA0gB_SYywonk4YGTXztvpaRiFu5HIOg7giI8zVLKShzmMAqwTEzHr3X7TQV-JxMdk4TKuB4Tl46wAMFc1jz3TkT9VBPd9V_lEJ8UzwpB1ODGw-NZwxgUspB7Wcbkq4CaAAOzbNGPsLr4rTqBvpQFxChH2qdN7fHVKpwrTjObzGZKRRFsL7jvROH5960yQ_OFHdgnaFDKJTY7IjfIevqQ",
  "dp": "moxXSSzMBQc27iMzxntrD0zLNCDhV5IK7bjB7eg3CbppyWCFlUJeTzR9y6R3zDtxGV2Vibri9hNuuD3qSyDZttUTAyuEyAxWgkQqIjzqPViZGhwR5QQUhmWNB-f8pR_Jc-6W8PsjjAI-6NWe6QiiF97yY4WD_BpmN78xkM6ieRk",
  "dq": "GjpLaxOFBpLKt4CzQxSF_Y8tstm3MgLgq8eHdCM86eNURQWS_O1LCP4qhjg2B1UVIR6iAONNHh3CW8bXJOFCVYs_Pt_MpLD0hlRbtLhpwthBWb8bO-meNgBlYy_CQtSMc-mnzGFoQ2EZTxCrrdoxhZIJ0hy-50L83diqQBcrWyU",
  "e": "AQAB",
  "ext": true,
  "key_ops": [
    "sign"
  ],
  "kty": "RSA",
  "n": "xY5LysZ9aXipdDEDUQuldfzSMo8q-M_mhE5ZyKXvitCOkKcgU5Te9ZGTFqgGGdOiFl5JKmnawxHPwUb1FTl3zg46k7CEyOSZ10hud0rIccqEDqeBMN_m4nfJr0D4eMdXFlU7l1lFNH531-QSg9OxTIO-UFrcdmARkAghQ0nfXmDl7-CYULD8quD2ItzUJVpiM2uxN5c8eIdEu_ugZaPQJnXYx-he0UEA01AQj0VhfqZW-Skmq-lEERTrUXqbeVPte6Hgc4PXzQ1JmM9v2W7P3NRqb9LVU8wcFO-0wMYzmn0cWcYttMM6wROxmMG-GxkDVcZr347r6ez2ZRmL7KvJrw",
  "p": "_VRjj8vjYMSrrsYeDZg01A__ZCeHKu8MYbS5ueo8ezNi7FUTJ_DcXyOehf0GhcosUCfYMf8VGOWcx2PJMrFJ0ryW55h6V-549EzCoyo5QZiy42L5y9D8svyTwyC_dAko6aDsPOri_U846mz5tClOSOw0Nku66RSEUqhNPo6z61U",
  "q": "x6NmvPNWOmPSZgafmXmFNKl0jb-4CdBjYwQzAv5s-YaEJK-LC7kdn1gsX3bhH6s3MLIDrDEtpwGAMb--ssQYOfd13VzPybIi014Tavt-sw1EREe-rW1rrl8gg-3Yte4FILWZiqaF8LLGQ341Wzd2ghkTkZbGnaPzawY_LNHlyPM",
  "qi": "IGkjXCUNVZS0a0irnGkyIh2npIJFQGmXAl-oUH82Yh5HdaeAq4DsdJENOfQjqIczx2RbGcHWEtwq8cVR0KtvQ2Xc8T6ItUYnmjW484CK1ZyCUgiBM8pv9Bh6GELcs9qCuF7A4HqQR6nq91MRRdXm8jRYcry1Ibb--k_MTMIf3K8"
}
EOD;

$publicKey = <<<EOD
{
  "alg": "RS256",
  "e": "AQAB",
  "ext": true,
  "key_ops": [
    "verify"
  ],
  "kty": "RSA",
  "n": "xY5LysZ9aXipdDEDUQuldfzSMo8q-M_mhE5ZyKXvitCOkKcgU5Te9ZGTFqgGGdOiFl5JKmnawxHPwUb1FTl3zg46k7CEyOSZ10hud0rIccqEDqeBMN_m4nfJr0D4eMdXFlU7l1lFNH531-QSg9OxTIO-UFrcdmARkAghQ0nfXmDl7-CYULD8quD2ItzUJVpiM2uxN5c8eIdEu_ugZaPQJnXYx-he0UEA01AQj0VhfqZW-Skmq-lEERTrUXqbeVPte6Hgc4PXzQ1JmM9v2W7P3NRqb9LVU8wcFO-0wMYzmn0cWcYttMM6wROxmMG-GxkDVcZr347r6ez2ZRmL7KvJrw"
}
EOD;

$header = [
  "alg" => "RS256",
  "typ" => "JWT",
  "kid" => $_ENV['LINE_LOGIN_KID']
];

$key = $_ENV['LINE_LOGIN_JWT_KEY'];

//function encode json web token using $header and $privateKey
function encodeJWT($payload) {
    global $header;
    global $key;
    $jwt = JWT::encode($payload, $key, 'HS256', null, $header);
    return $jwt;
}

//function check jwt
function checkJWT($jwt, $newKey=null) {
    global $key;
    $mykey = $newKey?$newKey:$key;
    try {
        $decoded = JWT::decode($jwt, new Key($mykey, 'HS256'));
        return $decoded;
    } catch (ExpiredException $e) {
        save_log("ExpiredException " . $e->getMessage());
        return false;
    } catch (SignatureInvalidException $e) {
        save_log("SignatureInvalidException " . $e->getMessage());
        return false;
    } catch (BeforeValidException $e) {
        save_log("BeforeValidException " . $e->getMessage());
        return false;
    } catch (UnexpectedValueException $e) {
        save_log("UnexpectedValueException " . $e->getMessage());
        return false;
    }
}


/* 
=== private key ===


=== public key ===

*/
?>