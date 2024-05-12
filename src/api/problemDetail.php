<?php

class ProblemDetail {
    public function __construct($statusCode, $title, $detail, $file = "", $line = "") {
        $this->statusCode = $statusCode;
        $this->type = "error-".$statusCode;
        $this->title = $title;
        $this->detail = $detail;
        $this->file = $file;
        $this->line = $line;
    }
    public $statusCode;
    public $type;
    public $title;
    public $detail;
    public $file;
    public $line;
}

class ProblemDetailException extends RuntimeException{
    public ProblemDetail $problemDetail;
    function __construct(ProblemDetail $problemDetail){
        $this->problemDetail = $problemDetail;
    }
}

function ThrowProblemDetail($statusCode, $title, $detail, $file = "", $line = "")
{
    $problemDetail = new ProblemDetail($statusCode, $title, $detail, $file, $line);
    $problemDetail->type = "error-".$statusCode;

    throw new ProblemDetailException($problemDetail);
}
