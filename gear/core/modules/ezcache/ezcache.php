<?php
class EzCache implements IEzCache
{
    private $_totalHash = [];

    public function set(string $k, string $v, int $expire = 7200): bool
    {
        if($this->has($k)){
            return false;
        }
        $this->_totalHash[$k] = [$v, time()+$expire];
        return true;
    }

    public function get(string $k)
    {
        if(!$this->has($k)){
            return null;
        }elseif($this->isExpire($k)){
            $this->remove($k);
            return null;
        }
        return $this->_totalHash[$k][0];
    }

    public function getAll(){
        return $this->_totalHash;
    }

    public function lpop(string $k, int $expire): bool
    {
        // TODO: Implement lcreate() method.
    }

    public function lpush(string $k, $v): bool
    {
        // TODO: Implement lpush() method.
    }

    private function remove($k)
    {
        if(!$this->has($k)){
            return false;
        }
        unset($this->_totalHash[$k]);
        return true;
    }

    private function has($k)
    {
        return isset($this->_totalHash[$k]);
    }

    private function isExpire($k)
    {
        if(!$this->has($k)){
            return true;
        }
        return time() > $this->_totalHash[$k][1];
    }
}