<?php
class HttpStatus
{
    private $code;
    private $status;

    public function __construct(int $code, String $status){
    }

    public function getCode(){
        return $this->code;
    }

    public function getStatus(){
        return $this->status;
    }

    public static function OK(){
        return new self(200, "OK");
    }

    public static function NOT_FOUND(){
        return new self(404, "NOT_FOUND");
    }

    public static function FORBIDDEN(){
        return new self(403, "Forbidden");
    }
}