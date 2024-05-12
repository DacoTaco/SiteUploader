<?php

//this is just a go through so i can easily adjust the user include file. uncomment what to use
require 'database/database.php';
require_once 'problemDetail.php';
require_once 'settings.php';
require_once 'jwt/JWT.php';
use Firebase\JWT\JWT;

class LoginResponse
{
    public string $username;
    public string $token;
    public function __construct(string $username, string $token)
    {
        $this->username = $username;
        $this->token = $token;
    }
}

function loginUser($username, $password): LoginResponse {
    global $database;
    global $jwtKey;
    $userEntry = $database->CheckUser($username, $password);
    if (!$userEntry) {
        ThrowProblemDetail(404, "Invalid User/pass", "Invalid User login info");
        exit(1);
    }

    //valid user, we need to generate a jwt token
    $time = time();
    $payload = [
        'iss' => $_SERVER["HTTP_HOST"],
        'aud' => $_SERVER["HTTP_HOST"],
        'iat' => $time,
        'nbf' => $time,
        'exp' => strtotime("+30 minutes"),
        'sub' => $userEntry->userName,
        'role' => $userEntry->type,
        'hash' => $userEntry->userHash,
    ];

    $jwt = JWT::encode($payload, $jwtKey, 'HS256');
    return new LoginResponse($userEntry->userName, $jwt);
}