<?php
class EzServerMaster{
    private $config = [
        "worker_count" => 2
    ];

    private $servers = [];

    public static function run($host, $port, $root){
        while(true){
            self::checkWorker();
        }
        $server = new EzServer();
        $server->init("127.0.0.1", "8888", "./")->start();
    }

    private static function checkWorker(){

    }
}