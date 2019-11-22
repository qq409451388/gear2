<?php
namespace gear\web;

class EzCookie{
    public static function set($key, $value, $livetime = 2592000, $domain = 'poethan.cn', $path = '/'){
        if ($livetime >= 0) {
            $livetime = time() + $livetime;
        }
        if (@setcookie($key, $value, $livetime, $path, $domain)) {
            $_COOKIE[$key] = $value;                                          
        }
        return true;
    }

    public static function setCookie4UserLogin($userName, $time = 7200, $domain = 'poethan.cn'){
        $userNameKey = self::encrypKey4UserName($userName);
        self::set('USER_NAME', $userName, $time, $domain);
        self::set('USER_CARD', $userNameKey, $time, $domain);
        return true;
    }

    public static function encrypKey4UserName($userName){
        return md5($userName.'flight');
    }

    public static function verfyCookie4UserLogin(){
        $userName = empty($_COOKIE['USER_NAME']) ? '' : $_COOKIE['USER_NAME'];
        $userCard = empty($_COOKIE['USER_CARD']) ? '' : $_COOKIE['USER_CARD'];
        if(empty($userName) || empty($userCard)){
            return false;
        }
        return $userCard == self::encrypKey4UserName($userName);
    }

    public static function getUserName(){
        return empty($_COOKIE['USER_NAME']) ? '' : $_COOKIE['USER_NAME'];
    }

}
