<?php
const BASE_PATH = __DIR__."/user/";
//env dev online
const ENV = 'online';
function getFilePaths($path = __DIR__, &$hash = [], &$userHash = [], $filter = [])
{

	//过滤掉点和点点
	$map = array_filter(scandir($path), function($var){
        return $var[0] != '.';
	});
	foreach ($map as $item) {
		$curPath = $path.'/'.$item;
		$userFlag = !strpos($path,"libs/");
		if(is_dir($curPath)){
		    if(in_array($item, $filter)){
		        continue;
            }
			if($item == '.' || $item == '..'){
				continue;
			}
			getFilePaths($curPath, $hash, $userHash, $filter);
		}
		if(false == strpos($item,".php")){
		    continue;
        }
		if(is_file($curPath)){
		    $className = strtolower(str_replace('.php','',$item));
			$hash[$className] = $curPath;
            if($userFlag){
                $userHash[$className] = $curPath;
            }
		}
	}
	return $hash;
}

function getFilePathHash()
{
    $hash = $userHash = [];
    getFilePaths(BASE_PATH, $hash, $userHash);
    return [$hash, $userHash];
}

list($hash, $userHash) = getFilePathHash();
spl_autoload_register(function ($className) use($hash){
    $className = strtolower($className);
    $filePath = empty($hash[$className]) ? '' : $hash[strtolower($className)];

    if(file_exists($filePath)){
    	include($filePath);
    }
});