<?php
ini_set('display_startup_errors',1); 
ini_set('display_errors',1);

require 'fileListing.php';
require 'users.php';
require_once 'problemDetail.php';
require_once 'settings.php';
require_once 'jwt/JWT.php';
require_once 'jwt/Key.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// error handler function
function ReplyProblemDetail(ProblemDetail $problemDetail)
{
    http_response_code($problemDetail->statusCode);
    echo json_encode($problemDetail);
    exit(1);
}
function GlobalErrorHandler($errno, $errstr, $errfile, $errline)
{
    $errstr = htmlspecialchars($errstr);
    if(!empty($errfile))
        $errfile = str_replace($_SERVER["DOCUMENT_ROOT"], "", $errfile);

    $problemDetail = new ProblemDetail(500, "Server done goofed", $errstr, $errfile, $errline);
    $problemDetail->type = "error-".$errno;
    ReplyProblemDetail($problemDetail);   
    exit(1);
}

function CheckRouteParameter(string $route)
{
    if(!isset($route) || empty($route))
        return false;
    
    return true;		
}

function ProcessGet(string $route, string $token)
{
    switch($route)
    {
        case "files":
            return GetFileEntries();
        default:
            ThrowProblemDetail(404, "Not Found", "Route '".$route."' not found");
    }
}

function ProcessPost(string $route, string $token)
{
    switch($route)
    {
        case "files":
            return SaveFileEntry();
        default:
            ThrowProblemDetail(404, "Not Found", "Route '".$route."' not found");
            return "";
    }
}

function ProcessCall()
{
    global $jwtKey;
    $route = $_GET["route"];
    if(!CheckRouteParameter($route))
    {
        ThrowProblemDetail(400, "Invalid Data", "Invalid call data: route url parameter is missing");
        return;
    }

    if (!isset($_SERVER["HTTP_AUTHORIZATION"]))
    {
        ThrowProblemDetail(400, "Invalid Authorization", "Invalid Authorization token given, token was not set");
        return;
    }

    $route = strtolower($route);
    $responseBody = "";
    $authorizationToken = $_SERVER["HTTP_AUTHORIZATION"];
    $tokenParts = explode(" ", $authorizationToken);
    if(count($tokenParts) != 2)
        ThrowProblemDetail(400, "Invalid Authorization token","The request had an invalid authorization token, or was missing.");

    //check if this is the login route. it is the only route in which we allow authorization to be not a bearer token (basic auth)
    if($route == "login")
    {
        if($tokenParts[0] != "Basic")
            ThrowProblemDetail(400, "Invalid token type","A basic token was expected when logging in.");

        $decoded = base64_decode($tokenParts[1]);
        list($username,$password) = explode(":",$decoded);
        $responseBody = loginUser($username, $password);
    }
    else
    {
        if($tokenParts[0] != "Bearer")
            ThrowProblemDetail(400, "Invalid token type","A Bearer token was expected when logging in.");

        //validate jwt token to make sure the user is authenticated :)
        $token = JWT::decode($tokenParts[1], new Key($jwtKey, 'HS256'));
        $httppMethod = $_SERVER["REQUEST_METHOD"];
        switch($httppMethod)
        {
            case "POST":
                $responseBody = ProcessPost($route, $tokenParts[1]);
                break;
            case "GET":
                $responseBody = ProcessGet($route, $tokenParts[1]);
                break;
            default:
                ThrowProblemDetail(400, "Invalid Http Method", "Unsupported Http Method '".$httppMethod."' sent");
        }
    }

    if(empty($responseBody))
		return "";
	
	if(gettype($responseBody) == "string")
		$responseBody = mb_convert_encoding($responseBody, 'UTF-8', 'ISO-8859-1');
	
	return json_encode($responseBody);
}

try
{
    // set to the user defined error handler
    // $old_error_handler = set_error_handler("GlobalErrorHandler");
    echo ProcessCall();
}
catch(\ProblemDetailException $ex)
{
    ReplyProblemDetail($ex->problemDetail);
}
catch(\Error $e)
{
    GlobalErrorHandler(500, $e->getMessage(), $e->getFile(), $e->getLine());
}
catch ( \Exception $ex )
{
    GlobalErrorHandler(500, $ex->getMessage(), $ex->getFile(), $ex->getLine());
}