<?php
Interface IAnno
{
    public function create(Array $item);
    public function save(String $key, Array $val);
    public function getClass();
    public function getMethod();
    public function getProperty();
}
