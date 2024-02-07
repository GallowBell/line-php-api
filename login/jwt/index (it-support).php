<?php 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;

$privateKey = <<<EOD
{
  "alg": "RS256",
  "d": "P-Dy6i29ArowwwwiRZ6hwTYsGbcs4XlGPh2ylEWRsE1Jddno-mV9WcA1OV1lpuSkrV2Pet9iwrC0PuON8YkEFdpNIFvdP73N-wplgxdQp3dTJX06abT3Lgq98LVxz-FXNpBN4r2y7aEGxhn2TGDESd0nYYLgsWbNVc3cL0eHEUqmynHKVn64m_WAOI8ZLuhe93Z1yRFff-bCQSfeMYftGUHrONd2EqjhlTAeOSsHHQHvWDJbWBE3tZjHA1WkpCCcGTKYBCrEBmUI-U83wl2RvkWwMeAQow_-KLNs2YnG-r5sJA93B__mXOW803CmlC-w3AXUcWoKyMEyHz605kScaQ",
  "dp": "lcVAFbD9zN6SKTPxglgWRFXEtc2QzdrF6yK_gG7F6kqo2MkX8c96_JYj9Kc5yzlMpmYBRgfpX0b6L91faFDAoR5KSPnOsPqO8LAswjY9r_GIVuxYhT6V_Ri8U5x2XbrnEs6Gk5Zqw_aISkUTTG9JJhao56ZWDem9hvZ6Rfkyl30",
  "dq": "RyY9rsBFaP4qINcrkIyk8wqbpyKGjfa5_IJmOx4dTaJBbtwmfHnJFrR0r18q3ux87P04XBUN2cg1rjqVGj04xzwGAlsqLNbGfcsgiqVDqv6tmpH0ekXv3k4384E3alopS6WU8lygxcpTDFEsxeSoCgACBsymRswl2W4q3DZy2-U",
  "e": "AQAB",
  "ext": true,
  "key_ops": [
    "sign"
  ],
  "kty": "RSA",
  "n": "vcqZduuWcHlMK-FUZO8qFKhMgQBU9jTfYXbrtnezkBvIDNoU_J3V4q2_6HKPiyIjoTrlwLZShJP8AL2OFyctOeTgiGcIDMFfPym3sKwuIWDAtisgOnxgsA7-k-WRb4WGgn1Y9W8WKMJJA2uu1U2amJv-9pRBIoJ7BzWeVs7LquMIHirs83V-NHS7GEiJ3KCYbYW6mUEiSAvoi4c26Oj-qjsmX1EfYfhOL5WhC8PzQz3y6vnaRC1jJgu8xwjb8EHOE6SxeyLyUQ1FL0b77cNj2rVkFKrx_LmEnlc5dcDLTdTpBOaOwmIspCB6rsVKOyYoGSbjri1UZcEvYWWdXgZPMQ",
  "p": "_jjSVgRLCsHJfx5szo-eYG7QdQdMJyttew0ve0Y7SGvOAMgwqPBe691CoKmfPSO8r6TRBGWuEb7LA-9_rEx-oGwSWRz_ywSgovFo0Od65wZTyOtL0pbMAJM0V70gJT6ppMa1jfauE8RoV2XXFk797VqhiDJgONxq4wZwj47czA8",
  "q": "vx5qnamKygrRt4fCId9c0Jbpqz38xf0v_ETG1LX9OjAL7m5QIOhobZoBQd8TmeuvQ5MEMJMrXU0oS-K9S9go15yVpn0pfO-E_W-Pv0IbaNioprPrDx0hDe9nPrIHNuSnBSvts0CQEZODwKsq8nq-Eix9OlinP0vTp1DxhixE8L8",
  "qi": "hERa67XXG2ycLi1yOo4xISbvpVX5eNTfCSXXa2ckeDkFAXJgnZS9S_TtXpccBADoglIllXx88PDH7dnhreRrlhKFn7gfL4S66T7guFInuYrQwzVwGaQCtWWhvxmX_zMX_xpUC-EduoVT7ZUeq8N6msqqyA1T-htBdBEDM8uSxeE"
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
  "n": "vcqZduuWcHlMK-FUZO8qFKhMgQBU9jTfYXbrtnezkBvIDNoU_J3V4q2_6HKPiyIjoTrlwLZShJP8AL2OFyctOeTgiGcIDMFfPym3sKwuIWDAtisgOnxgsA7-k-WRb4WGgn1Y9W8WKMJJA2uu1U2amJv-9pRBIoJ7BzWeVs7LquMIHirs83V-NHS7GEiJ3KCYbYW6mUEiSAvoi4c26Oj-qjsmX1EfYfhOL5WhC8PzQz3y6vnaRC1jJgu8xwjb8EHOE6SxeyLyUQ1FL0b77cNj2rVkFKrx_LmEnlc5dcDLTdTpBOaOwmIspCB6rsVKOyYoGSbjri1UZcEvYWWdXgZPMQ"
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