<?php

require_once 'settings.php';
require_once 'problemDetail.php';

class FileEntry {
    public string $filename;
    public string $path;
}

// Custom comparison function
function compareFileEntries($a, $b) {
    return $a->filename <=> $b->filename;
}

function IsAcceptedFile(string $filename):bool
{
    if(!isset($filename))
        return false;

    $filename = basename($filename);
    if($filename == "." || $filename == ".." || is_dir($filename) )
        return false;
    
    if($filename == ".htaccess" || $filename == "robots.txt")
        return false;

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    switch($ext)
    {
        case "htm":
        case "html":
        case "css":
        case "js":
        case "php":
            return false;
        default:
            break;
    }

    return true;
}

function GetFileEntries()
{
    global $filesRoot;

    $arr = array();
    $TrackDir = opendir($filesRoot);
    $filePath = str_replace($_SERVER['DOCUMENT_ROOT']."/", "/", $filesRoot)."/";
    while ($file = readdir($TrackDir)) 
    { 
        if(!IsAcceptedFile($file))
            continue;

        $fileEntry = new FileEntry();
        $fileEntry->filename = $file;
        $fileEntry->path = $filePath.$file;
        $arr[] = $fileEntry;
    }

    //sort the array using our function above
    usort($arr, 'compareFileEntries');
    return $arr;
}

function SaveFileEntry()
{
    if(!isset($_FILES) || empty($_FILES))
    {
        ThrowProblemDetail(400, "Invalid Request", "No files were provided to save to server");
        return;
    }

    global $filesRoot;

    $fileData = $_FILES['filedata'];
    $tempFile = $fileData['tmp_name'];
	$targetFile = rtrim($filesRoot,'/') . '/' . $fileData['name'];
	
	// Validate the file type
    if(!IsAcceptedFile($targetFile))
    {
        ThrowProblemDetail(400, "File rejected", "the file was not accepted because it is an invalid type");
        return;
    }

    //move the file that php put in the tmp folder and move it to the end directory
    move_uploaded_file($tempFile,$targetFile);
    $file = basename($targetFile);
    $fileEntry = new FileEntry();
    $fileEntry->filename = $file;
    $fileEntry->path = $file;

    return $fileEntry;
}