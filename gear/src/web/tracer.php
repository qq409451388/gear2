<?php
class Tracer extends Container {
    private $s1;
    private $s2;
    public function start(){
        $this->s1 = microtime(true);
    }

    public function end(){
        $this->s2 = microtime(true);
    }

    //计算时间消耗，并转为毫秒
    public function finish(){
        $this->end();
        $time = $this->s2 - $this->s1;
        return round($time * 1000, 3);
    }

    public function log($msg = ''){
        $time = $this->finish();
        if(!empty($msg)) {
            $msg = date('Y/m/d H:i:s ').$msg.'  ';
        }
        $msg .= '[consume:'.$time.' ms]'.PHP_EOL;
        Logger::save($msg, __CLASS__);
    }
}