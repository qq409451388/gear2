<?php
/**
 * Http 服务器类
 */
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
     * @param int $code 状态码
     * @param string $status 状态名称
     * @param string $content 发送的文本内容
     * @param string $content_type 发送的内容类型
     * @return string
     **/
    public function getHeaders($code,$status,$content="",$content_type="text/html;charset=utf-8"):String{
        $header = '';
        $header .= "HTTP/1.1 {$code} {$status}\r\n";
        $header .= "Date: ".gmdate('D, d M Y H:i:s T')."\r\n";
        $header .= "Content-Type: {$content_type}\r\n";
        $header .= "Content-Length: ".strlen($content)."\r\n\r\n";//必须2个\r\n表示头部信息结束
        $header .= $content;
        return $header;
    }

    private function getResponse($path, $params):String{
        if(empty($path) || empty($params)){
            return $this->getHeaders(404, "Not Found");
        }
        $item = $this->judgePath($path);
        if(null == $item){
            if(empty($this->_root)){
                return $this->getHeaders(404, "Not Found");
            }
            $content = $this->getStaticResponse($path);
        }else{
            $content = $this->getDynamicResponse($item, $params);
        }
        return $this->getHeaders(200,"OK", $content, $this->getMime($path));
    }

    private function judgePath($path){
        $item = $this->gear->getMapping('/'.$path);
        return null != $item && $item instanceof MapItem ? $item : null;
    }

    private function getDynamicResponse(MapItem $item, Array $params):String{
        return $this->gear->invokeMethod($item, $params);
    }

    private function getStaticResponse($path):String{
        if(!$this->getRealPath($path)) {
            return $this->getHeaders(404, "Not Found");
        }
        if(!is_readable($this->getRealPath($path))){
            return $this->getHeaders(403, "Unauthorized");
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