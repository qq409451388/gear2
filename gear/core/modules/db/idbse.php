<?php
Interface IDbSe
{
    public function query(String $sql, Array $binds = []);
    public function findOne(String $sql, Array $binds = []);
    public function queryColumn(String $sql, Array $binds, String $column);
    public function queryHash(String $sql, Array $binds, String $key, String $value);
    public function queryGroup(String $sql, Array $binds, String $groupBy, String $val);
    public function queryValue(String $sql, Array $binds, String $value);
}