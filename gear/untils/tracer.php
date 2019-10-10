<?php
namespace gear\untils;

use gear\untils\Logger;

class Tracer
{
    private $s = [];
    public function start(){
        $this->s['start'] = microtime(true);
    }

    public function add($node){
        if(empty($node)){
            Assert::argEx("[Tracer] UnKnow Node $node");
        }
        $this->s[$node] = microtime(true);
    }

    public function end(){
        $this->s['end'] = microtime(true);
    }

    //计算时间消耗，并转为毫秒
    public function finish(){
        $this->end();
        $time = $this->s['end'] - $this->s['start'];
        return round($time * 1000, 3);
    }

    public function log($msg = ''){
        $time = $this->finish();
        if(!empty($msg)) {
            $msg = date('Y/m/d H:i:s ').$msg.'  ';
        }
        $msg .= '[consume:'.$time.' ms]'.PHP_EOL;
        Logger::save($msg);
    }
}
