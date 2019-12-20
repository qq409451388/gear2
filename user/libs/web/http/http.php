<?php
class HTTP{
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
        'ico' => 'image/x-icon',
        'ico' => 'image/webp',
        'jpe' => 'image/jpeg',
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

    public function init($host, $port, $root = ''){
        $this->host = $host;
        $this->port = $port;
        $this->_root = $root;
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
                $buf = socket_read($msgsocket, 9024);
                if(Env::isDev()){
                    Logger::console("============================================================\n[request]{$buf}\n============================================================");
                }
                //获取接收文件类型
                $accept = $this->getAccept($buf);
                //检查请求类型
                $this->check($accept);
                //获取web路径
                $webPath = $this->getPath($buf);
                list($path, $args) = $this->parseUri($webPath);
                $content = $this->getResponse($path, $args);
                socket_write($msgsocket, $content, strlen($content));
                socket_close($msgsocket);
            }
        }
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

    private function getResponse($path, $params):String{
        if(empty($path)){
            return $this->getHeaders(HttpStatus::OK());
        }
        $judged = $this->judgePath($path);
        if(empty($judged)){
            //TODO 用作静态服务器时开启
            /*if(empty($this->_root)){
                return $this->getHeaders(HttpStatus::NOT_FOUND());
            }
            $content = $this->getStaticResponse($path);*/
            $content = EzHttpResponse::EMPTY_RESPONSE;
        }else{
            $content = $this->getDynamicResponse($judged, $params);
        }
        return $this->getHeaders(HttpStatus::OK(), $content, $this->getMime($path));
    }

    private function judgePath($path){
        return $this->gear->getMapping($path);
    }

    private function getDynamicResponse(Array $item, Array $params):String{
        return $this->gear->invokeMethod($item, $params);
    }

    private function getStaticResponse($path):String{
        if(!$this->getRealPath($path)) {
            return $this->getHeaders(HttpStatus::NOT_FOUND());
        }
        if(!is_readable($this->getRealPath($path))){
            return $this->getHeaders(HttpStatus::FORBIDDEN());
        }
        $realPath = $this->getRealPath($path);
        $fileContent = file_exists($realPath) ? file_get_contents($realPath) : ' ';
        return $fileContent;
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
