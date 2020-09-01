<?php
class HTTP implements IHttp {
    private $host;
    private $port;
    private $_root;
    private $gear;

    public $mime_types = array(
        'avi' => 'video/x-msvideo',
        'bmp' => 'image/bmp',
        'css' => 'text/css',
        'doc' => 'application/msword',
        'gif' => 'image/gif',
        'htm' => 'text/html',
        'html' => 'text/html',
        //'ico' => 'image/x-icon',
        'ico' => 'image/webp',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'application/x-javascript',
        'mpeg' => 'video/mpeg',
        'ogg' => 'application/ogg',
        'png' => 'image/png',
        'rtf' => 'text/rtf',
        'rtx' => 'text/richtext',
        'swf' => 'application/x-shockwave-flash',
        'wav' => 'audio/x-wav',
        'wbmp' => 'image/vnd.wap.wbmp',
        'zip' => 'application/zip',
    );

    public function __construct(Gear $gear){
        $this->gear = $gear;
    }

    public function init(string $host, $port, $root = ''){
        $this->host = $host;
        $this->port = $port;
        $this->_root = $root;
        Config::setEnvInfo(['host'=>$host, 'port'=>$port]);
        return $this;
    }

    /**
     * 启动http服务
     */
    public function start(){
        Logger::console("[start http server]...");
        //创建socket套接字
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        //设置阻塞模式
        socket_set_block($socket);
        //为套接字绑定ip和端口
        socket_bind($socket,$this->host,$this->port);
        //监听socket
        socket_listen($socket,4);
        Logger::console("[start success]".$this->host.":".$this->port);
        while(true)
        {
            //接收客户端请求
            if($msgsocket = socket_accept($socket)){
                //读取请求内容
                $buf = socket_read($msgsocket, 8192);
                //获取接收文件类型
                $accept = $this->getAccept($buf);
                //检查请求类型
                $this->check($accept);
                //获取web路径
                $webPath = $this->getPath($buf);
                list($path, $args) = $this->parseUri($webPath);
                $request = $this->buildRequest($buf, $args);
                $content = $this->getResponse($path, $request);
                socket_write($msgsocket, $content, strlen($content));
                socket_close($msgsocket);
            }
        }
    }

    private function buildRequest($buf, $args){
        $request = new Request();
        $httpOptions = explode("\r\n", $buf);
        $requestBody = end($httpOptions);
        parse_str($requestBody, $requestBodyArr);
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

    private function parseUri($webPath){
        $pathArr = parse_url($webPath);
        $path = $pathArr['path'] ?? '';
        $query = $pathArr['query'] ?? '';
        parse_str($query, $args);
        return [$path, $args];
    }

    private function getPath($buf){
        preg_match("/\/(.*) HTTP\/1\.1/",$buf,$path);
        return end($path);
    }

    private function getAccept($buf){
        preg_match("/Accept: (.*?),/",$buf,$accept);
        return end($accept);
    }

    private function check($type){
        if(!in_array($type, array_values($this->mime_types))){
            Logger::console("[EzServer] UnSupport Type : {$type}");
        }
    }

    /**
     * 组装消息头信息模板
     * @param HttpStatus $httpStatus 状态
     * @param string $content 发送的文本内容
     * @param string $contentType 发送的内容类型
     * @return string
     **/
    public function getHeaders(HttpStatus $httpStatus, $content = "", $contentType = "text/html"):String{
        return (new EzHeader($httpStatus, $content, $contentType))->get();
    }

    public function getResponse(string $path, $request):string{
        if(empty($path)){
            $content = "<h1>It Works!</h1>";
            return $this->getHeaders(HttpStatus::OK(), $content);
        }
        $judged = $this->judgePath($path);
        if(!$judged){
            if(empty($this->_root)){
                return $this->getHeaders(HttpStatus::NOT_FOUND());
            }
            $content = $this->getStaticResponse($path);
        }else{
            $content = $this->getDynamicResponse($path, $request);
        }
        return $this->getHeaders(HttpStatus::OK(), $content, $this->getMime($path));
    }

    private function judgePath($path){
        return $this->gear->judgePath($path);
    }

    public function getDynamicResponse(string $path, $request):string{
        return $this->disPatch($path, $request);
    }

    public function disPatch(string $path, $request){
        return $this->gear->disPatch($path, $request);
    }

    public function getStaticResponse(string $path):string{
        return EzHttpResponse::EMPTY_RESPONSE;
        /*if(!$this->getRealPath($path)) {
            return $this->getHeaders(HttpStatus::NOT_FOUND());
        }
        if(!is_readable($this->getRealPath($path))){
            return $this->getHeaders(HttpStatus::FORBIDDEN());
        }
        $realPath = $this->getRealPath($path);
        return file_exists($realPath) ? file_get_contents($realPath) : ' ';*/
    }

    /**
     * 获取资源类型
     * @param string $path
     * @return mixed
     */
    public function getMime($path){
        $type = explode(".",$path);
        return $this-> mime_types[end($type)] ?? 'text/html';
    }

    /**
     * 获取访问资源的真实地址
     * @param $url_path
     * @return bool|string
     */
    public function getRealPath($url_path){
        return realpath($this->_root."/".$url_path);
    }

}