<?php
Interface IAnno
{
    public function save(String $key, AnnoItem $mapItem);
    public function saveAll(String $resClass, Array $resMethods, String $className);
    public function get(String $key):AnnoItem;
}