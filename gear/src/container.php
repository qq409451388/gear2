<?php
namespace gear;

use gear\untils\Assert;

class Container
{
    private static $map = [];

	public static function get($className)
    {
	    if(empty($className)){
            Assert::argEx("[Container]no class $name", -1);
        }
        if (empty(self::$map[$className])) {
            self::register($className);
        }
        return self::$map[$className];    
    }

    public function register($className, $object = null)
    {
        if(!is_null($object)){
            self::$map[$className] = $object;
        }else{
            self::$map[$className] = new $className;
        } 
    }

}
