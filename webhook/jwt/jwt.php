<?php 

//function check jwt
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;

$key = 'example_key';
$payload = [
    'iss' => 'http://example.org',
    'aud' => 'http://example.com',
    'iat' => 1356999524,
    'nbf' => 1357000000,
    'env' => [
        'S3_BUCKET' => $_ENV['S3_BUCKET'],
        'SECRET_KEY' => $_ENV['SECRET_KEY'] 
    ]
];


?>