<?php
/**
 * Http 服务器类
 */
class EzServer{
    private $host;
    private $port;
    private $_root;

    public $mime_types = array(
        'avi' => 'video/x-msvideo',
        'bmp' => 'image/bmp',
        'css' => 'text/css',
        'doc' => 'application/msword',
        'gif' => 'image/gif',
        'htm' => 'text/html',
        'html' => 'text/html',
        'ico' => 'image/x-icon',
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

    /**
     * @param string $host 监听地址
     * @param int $port 监听端口
     * @param string $_root 网站根目录
     * @return EzServer
     */
    public function init($host,$port,$_root){
        $this->host = $host;
        $this->port = $port;
        $this->_root = $_root;
        return $this;
    }

    /**
     * 启动http服务
     */
    public function start(){
        //创建socket套接字
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        //设置阻塞模式
        socket_set_block($socket);
        //为套接字绑定ip和端口
        socket_bind($socket,$this->host,$this->port);
        //监听socket
        socket_listen($socket,4);

        while(true)
        {
            //接收客户端请求
            if($msgsocket = socket_accept($socket)){
                //读取请求内容
                $buf = socket_read($msgsocket, 9024);
                //获取接收文件类型
                $accept = $this->getAccept($buf);
                //获取访问得文件类型
                $path = $this->getPath($buf);
                //检查请求类型
                $this->check($accept);
                $content = $this->getResponse($path);
                socket_write($msgsocket,$content,strlen($content));
                socket_close($msgsocket);
            }
        }
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
            Assert::runtimeEx("[Server UnSupport Type{$type}]");
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
    public function getHeaders($code,$status,$content="",$content_type="text/html;charset=utf-8"){
        $header = '';
        $header .= "HTTP/1.1 {$code} {$status}\r\n";
        $header .= "Date: ".gmdate('D, d M Y H:i:s T')."\r\n";
        $header .= "Content-Type: {$content_type}\r\n";
        $header .= "Content-Length: ".strlen($content)."\r\n\r\n";//必须2个\r\n表示头部信息结束
        $header .= $content;
        return $header;
    }

    private function getResponse($path){
        if(!$this->getRealPath($path)) {
            Assert::runtimeEx("[Server Not Found]");
        }
        if(!is_readable($this->getRealPath($path))){
            Assert::runtimeEx("[Server Unauthorized]");
        }
        return $this->getHeaders(200,"OK",file_get_contents($this->getRealPath($path)),$this->getMime($path));
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