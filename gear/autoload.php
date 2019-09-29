<?php
/*
	未加载类之前定义全局常量
 */
const LIB_PATH = __DIR__;

//dev prod
const ENV = 'dev';
function getFilePaths($path = __DIR__, &$hash = []){
	//过滤掉点和点点
	$map = array_filter(scandir($path), function($var){
        return $var[0] != '.';
	});
	foreach($map as $item){
		$curPath = $path.'/'.$item;
		if(is_dir($curPath)){
			if($item == '.' || $item == '..'){
				continue;
			}
			getFilePaths($curPath, $hash);
		}
		if(is_file($curPath)){
			$hash[strtolower(str_replace('.php','',$item))] = $curPath;
		}
	}
	return $hash;
}

function getFilePathHash(){
	$file = __DIR__.'/pathCache.json';
	if(ENV != 'dev' && file_exists($file)){
		return json_decode(file_get_contents($file), true);
	}else{
		$hash = [];
        getFilePaths(LIB_PATH, $hash);
		file_put_contents($file, json_encode($hash));
		return $hash;
	}
}
$hash = getFilePathHash();
spl_autoload_register(function ($className) use($hash){
    $filePath = empty($hash[strtolower($className)]) ? '' : $hash[strtolower($className)];

    if(file_exists($filePath)){
    	include($filePath);
    }
});