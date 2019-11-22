<?php
/**
 * Http ��������
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
     * @param string $host ������ַ
     * @param int $port �����˿�
     * @param string $_root ��վ��Ŀ¼
     * @return EzServer
     */
    public function init($host,$port,$_root){
        $this->host = $host;
        $this->port = $port;
        $this->_root = $_root;
        return $this;
    }

    /**
     * ����http����
     */
    public function start(){
        //����socket�׽���
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        //��������ģʽ
        socket_set_block($socket);
        //Ϊ�׽��ְ�ip�Ͷ˿�
        socket_bind($socket,$this->host,$this->port);
        //����socket
        socket_listen($socket,4);

        while(true)
        {
            //���տͻ�������
            if($msgsocket = socket_accept($socket)){
                //��ȡ��������
                $buf = socket_read($msgsocket, 9024);
                //��ȡ�����ļ�����
                $accept = $this->getAccept($buf);
                //��ȡ���ʵ��ļ�����
                $path = $this->getPath($buf);
                //�����������
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
     * ��װ��Ϣͷ��Ϣģ��
     * @param int $code ״̬��
     * @param string $status ״̬����
     * @param string $content ���͵��ı�����
     * @param string $content_type ���͵���������
     * @return string
     **/
    public function getHeaders($code,$status,$content="",$content_type="text/html;charset=utf-8"){
        $header = '';
        $header .= "HTTP/1.1 {$code} {$status}\r\n";
        $header .= "Date: ".gmdate('D, d M Y H:i:s T')."\r\n";
        $header .= "Content-Type: {$content_type}\r\n";
        $header .= "Content-Length: ".strlen($content)."\r\n\r\n";//����2��\r\n��ʾͷ����Ϣ����
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
     * ��ȡ��Դ����
     * @param string $path
     * @return mixed
     */
    public function getMime($path){
        $type = explode(".",$path);
        return $this-> mime_types[end($type)] ?? 'text/html';
    }

    /**
     * ��ȡ������Դ����ʵ��ַ
     * @param $url_path
     * @return bool|string
     */
    public function getRealPath($url_path){
        return realpath($this->_root."/".$url_path);
    }

}