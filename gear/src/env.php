<?php
//初始化环境及全局环境变量
namespace gear;
class Env{
	public static function isShowError(){
		return self::isDev();
	}

	public static function isDev(){
		return ENV == 'dev';
	}
}
