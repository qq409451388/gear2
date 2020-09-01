<?php
class EzIp implements IEzIp
{
    public static function getInfo(string $ip): EzIpInfo
    {
        $url = "http://ipinfo.io/".$ip;
        $res = (new EzCurl())->setUrl($url)->get();
        $res = EzCollection::decodeJson($res);
        var_dump($res);
        $ezIpInfo = new EzIpInfo($res['ip'], $res['country'], $res['region'], $res['city']);
        return $ezIpInfo;
    }
}