<?php
class AnnoFactory
{
    private static $map = [];
    public static function create($type):IAnno{
        switch($type){
            case 'http':
                return Mapper::init();
                break;
            default:
                return new NullAnno();
        }
    }

}