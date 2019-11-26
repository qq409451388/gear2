<?php
/**
 * Http 服务器类
 */
class EzServer{
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
     * 启动http服务
     */
    public function start(){
        while(true){
            if(($buf = $this->getRequest())) {
                //获取接收文件类型
                $accept = $this->getAccept($buf);
                //获取访问得文件类型
                $path = $this->getPath($buf);
                //检查请求类型
                $this->check($accept);
                $content = $this->getResponse($path);
                socket_write($msgsocket, $content, strlen($content));
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