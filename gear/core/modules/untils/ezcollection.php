<?php
class EzCollection
{
    public static function decodeJson(string $json){
        return json_decode($json, true);
    }

    /**
     * @param $sourceHash
     * @paramformat array(item1,item2,...)
     * @describe for key => val n => 1
     * @returnformat array(item, array(item1,item2,...))
     */
    public static function collectValueGroup($sourceHash){
        $target = [];
        foreach($sourceHash as $k => $v){
            $target[$v][] = $k;
        }
        return $target;
    }

    /**
     * @param $sourceGroup
     * @paramformat array(item, array(item1,item2,...))
     * @describe for key => val 1 => n
     * @returnformat array(item1,item2,...)
     */
    public static function collectValueHash($sourceGroup){
        $target = [];
        foreach($sourceGroup as $k => $v){
            $target += array_fill_keys($v, $k);
        }
        return $target;
    }
}