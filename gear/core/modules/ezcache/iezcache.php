<?php
Interface IEzCache
{
    public function set(string $k, string $v, int $expire = 7200):bool;
    public function get(string $k);
    public function lpop(string $k, int $expire):bool;
    public function lpush(string $k, $v):bool;
}