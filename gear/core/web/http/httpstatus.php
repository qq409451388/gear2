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

    public static function INTERNAL_SERVER_ERROR(){
        return new self(500, "Internal Server Error");
    }

    public static function BAD_GATEWAY(){
        return new self(502, "Bad Gateway");
    }

    public static function GATEWAY_TIMEOUT(){
        return new self(504, "Gateway Timeout");
    }
}