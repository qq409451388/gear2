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

    public function __get($key){
        return $this->config[$key];
    }

    public function run(){
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
                $fileName = $this->sessionPath.uniqid();
                file_put_contents($fileName, $buf);
            }
        }
    }

    private static function checkWorker(){

    }
}