<?php
namespace gear\untils;

class DataTransfer{
	public static function toArray($obj){
		$arr = [];
		foreach($obj as $k => $v){
			if(is_object($v)){
				$v = self::toArray($v);
			}
			$arr[$k] = $v;
		}
		return $arr;
	}

	public static function toJson($source){
		if(is_object($source)){
			$arr = self::toArray($source);
			return self::toJson($arr);
		}
		if(is_array($source)){
			return json_encode($source);
		}
		return '[]';
	}

	public static function toSimpleString($obj, $char = '_'){
		$str = '';
		foreach($obj as $k => $v){
			$str .= $v.$char;
		}
		$str = trim($str, $char);
		return empty($str) ? 'null' : $str;
	}

	public static function cutMiddleStr($begin,$end,$str){
    	$b = mb_strpos($str,$begin) + mb_strlen($begin);
    	$e = mb_strpos($str,$end) - $b;
    	return mb_substr($str,$b,$e);
	}

	public static function real_encode($arr) {
	    $json_str = "";
	    if (is_array($arr)) {
	        $pure_array = true;
	        $array_length = count($arr);
	        for($i=0;$i<$array_length;$i++) {
	            if(! isset($arr[$i])) {
	                $pure_array = false;
	                break;
	            }
	        }
	        if($pure_array) {
	            $json_str ="[";
	            $temp = array(); 
	            for($i=0;$i<$array_length;$i++)
	            {
	                $temp[] = sprintf("%s", self::real_encode($arr[$i]));
	            }
	            $json_str .= implode(",",$temp);
	            $json_str .="]";
	        } else {
	            $json_str ="{";
	            $temp = array(); 
	            foreach($arr as $key => $value) {
	                $temp[] = sprintf('"%s":%s', $key, self::real_encode($value));
	            }
	            $json_str .= implode(",",$temp);
	            $json_str .="}";
	        }
	    } else {
	        if(is_string($arr)) {
	            $json_str = '"'. self::encode_string($arr) . '"';
	        } else if(is_numeric($arr)) {
	            $json_str = $arr;
	        } else {
	            $json_str = '"'. self::encode_string($arr) . '"';
	        }
	    } 
	    return $json_str;
	} 

	private static function encode_string($str) {
    	return str_replace('/', '\\/', str_replace('"', '\\"', str_replace("\n", "\\n", str_replace("\r", "", $str))));
	}


    /**
     *   只替换指定字符串在目标字符串的第一次出现
     * @param $needle
     * @param $replace
     * @param $haystack
     * @return mixed
     */
    public static function str_replace_once($needle, $replace, $haystack)
    {
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            return $haystack;
        }
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }
}
