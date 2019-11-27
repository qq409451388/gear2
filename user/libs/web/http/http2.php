<?php
class Http2
{
    private $host;
    private $port;
    private $gear;
    private $swoole;

    public function __construct(Gear $gear){
        $this->gear = $gear;
    }

    public function init($host, $port){
        $this->host = $host;
        $this->port = $port;
        $this->initSwoole();
        return $this;
    }

    private function initSwoole(){
        if(empty($this->host) || empty($this->port)){
            DBC::throwEx("[HTTP 2 Exception] init swoole ");
        }
        $this->swoole = new Swoole\Http\Server($this->host, $this->port);
    }

    public function start(){
        $this->swoole->on('request', function ($request, $response) {
            $response->end($html);
        });
        $this->swoole->start();
    }
}