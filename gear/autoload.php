<?php
const CORE_PATH = __DIR__ . "/core";
const USER_PATH = __DIR__ . "/avatar";

function getFilePaths($path = CORE_PATH, &$hash = [], $filter = [])
{

    //过滤掉点和点点
    $map = array_filter(scandir($path), function($var){
        return $var[0] != '.';
    });
    foreach ($map as $item) {
        $curPath = $path.'/'.$item;
        if(is_dir($curPath)){
            if(in_array($item, $filter)){
                continue;
            }
            if($item == '.' || $item == '..'){
                continue;
            }
            getFilePaths($curPath, $hash, $filter);
        }
        if(false == strpos($item,".php")){
            continue;
        }
        if(is_file($curPath)){
            $className = strtolower(str_replace('.php','',$item));
            $hash[$className] = $curPath;
        }
    }
    return $hash;
}

function getFilePathHash()
{
    $hash = $userHash = [];
    getFilePaths(CORE_PATH, $hash);
    getFilePaths(USER_PATH, $userHash, ['templates']);
    return [$hash, $userHash];
}

list($hash, $userHash) = getFilePathHash();
$userClasses = array_keys($userHash);
$hash += $userHash;
spl_autoload_register(function ($className) use($hash){
    $className = strtolower($className);
    $filePath = empty($hash[$className]) ? '' : $hash[strtolower($className)];

    if(file_exists($filePath)){
        include($filePath);
    }
});