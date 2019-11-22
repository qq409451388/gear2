<?php
class EzServerMaster{
    private $host;
    private $port;
    private $_root;

    private $config = [
        "worker_count" => 2,
        "sessionPath" => "/tmp/"
    ];

    private $servers = [];

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

    public function __get($key){
        return $this->config[$key];
    }

    public function run($host, $port, $root){
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
                $fileName = $this->sessionPath.uniqid();
                file_put_contents($fileName, $buf);
            }
        }
    }

    private static function checkWorker(){

    }
}