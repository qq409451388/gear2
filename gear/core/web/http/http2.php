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
        Config::setEnvInfo(['host'=>$host, 'port'=>$port]);
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
        Logger::console("[start http server]...");
        $this->swoole->on('request', function ($request, $response) {
            list($path, $args) = $this->parseRequest($request);
            $req = $this->buildRequest($request);
            $html = $this->getResponse($path, $req);
            $response->end($html);
        });
        Logger::console("[start success]".$this->host.":".$this->port);
        $this->swoole->start();
    }

    private function buildRequest($reqSwoole){
        $args = $reqSwoole->get ?? [];
        $requestBodyArr = $reqSwoole->post ?? [];
        $request = new Request();
        foreach($requestBodyArr as $k => $v){
            $request->setRequest($k, $v);
        }
        foreach($args as $k => $v){
            $request->setRequest($k, $v);
        }
        $requestMethod = null;
        if(!empty($args)){
            $requestMethod = 'get';
        }
        if(!empty($requestBodyArr)){
            $requestMethod = is_null($requestMethod) ? 'post' : 'mixed';
        }
        $request->setRequestMethod($requestMethod);
        return $request;
    }

    private function parseRequest($request){
        $path = $request->server['path_info'] ?? '';
        $query = $request->server['query_string'] ?? '';
        parse_str($query, $args);
        $path = trim($path, '/');
        return [$path, $args];
    }

    private function getResponse($path, $request){
        if(empty($path)){
            return EzHttpResponse::EMPTY_RESPONSE;    
        }
        return $this->gear->disPatch($path, $request);
    }
}
