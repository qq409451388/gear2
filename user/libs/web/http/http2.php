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
            list($path, $args) = $this->parseRequest($request);
            $html = $this->getResponse($path, $args);
            $response->end($html);
        });
        $this->swoole->start();
    }

    private function parseRequest($request){
        $path = $request->server['path_info'] ?? '';
        $query = $request->server['query_string'] ?? '';
        parse_str($query, $args);
        return [$path, $args];
    }

    private function getResponse($path, $args){
        if(empty($path)){
            return EzHttpResponse::EMPTY_RESPONSE;    
        }
        $item = $this->gear->getMapping($path);
        return $this->getDynamicResponse($item, $args);    
    }

    private function getDynamicResponse(AnnoItem $item, Array $params):String{
        return $this->gear->invokeMethod($item, $params);
    }
}
