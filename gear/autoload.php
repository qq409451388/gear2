<?php
const LIB_PATH = __DIR__;
const SRC_PATH = LIB_PATH.'/src';

//dev prod
const ENV = 'dev';
function getFilePaths($path = __DIR__, $folder = 'gear', &$hash = []){
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
			$tmpFolder = $folder.'\\'.$item;
			getFilePaths($curPath, $tmpFolder, $hash);
		}
		if(false == strpos($item,".php")){
		    continue;
        }
		if(is_file($curPath)){
		    $className = $folder.'\\'.$item;
		    $className = strtolower(str_replace('.php','',$className));
			$hash[$className] = $curPath;
		}
	}
	return $hash;
}

function getFilePathHash(){
	$file = __DIR__.'/pathCache.php';
	if(file_exists($file) && ENV != 'dev'){
        return include($file);
	}else{
		$hash = [];
        getFilePaths(SRC_PATH, 'gear',$hash);
        file_put_contents(__DIR__."/pathCache.php", "<?php return ".var_export($hash, true).';');
        return $hash;
	}
}
$hash = getFilePathHash();
spl_autoload_register(function ($className) use($hash){
    $className = strtolower($className);
    $filePath = empty($hash[$className]) ? '' : $hash[strtolower($className)];

    if(file_exists($filePath)){
    	include($filePath);
    }
});
