<?php

$filesRoot = $_SERVER['DOCUMENT_ROOT'];

//do make this random, mkey?
$jwtKey = "RandomKeyHere";

//base64 encoded enc key , which should be unique to every user of this code. 
//to generate a key do something like str_replace("==","",strtoupper(base64_encode(hash( 'sha256', mt_rand()))));
//this will generate a random number, sha256 hash it and base64 encode it.
$db_ENC_KEY = 'AnotherRandomKeyHere';
$db_host = "";
$db_user = "";
$db_pass = "";
$db_dbase = "";