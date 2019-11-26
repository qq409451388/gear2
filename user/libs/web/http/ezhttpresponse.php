<?php
class EzHttpResponse
{
    public $errCode;
    public $data;
    public $msg;

    private const OK = 0;

    public function __construct($data = [], $errCode = 0, $msg = ""){
        $this->errCode = $errCode;
        $this->data = $data;
        $this->msg = $msg;
    }

    public static function OK($data, $msg = ""){
        return new self($data,self::OK, $msg);
    }

    public function toJson():String{
        return EzString::encodeJson($this)??'{}';
    }
}