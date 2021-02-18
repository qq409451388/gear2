<?php
class AuthUntils
{
    private static $header = [
        'alg'=>'HS256', //生成signature的算法
        'typ'=>'JWT'  //类型
    ];

    private static $payloadParams = [
        'iat','exp','nbf'
    ];

    private const SECRET = "gear2@poethan";

    /**
     * @param $data   array  需要加密的原始数据
     * @param $exp    int   过期时间
     * @return string   token
     */
    public static function generateToken($data, $exp = 600):string {
        $data['iat'] = time();
        $data['exp'] = time()+$exp;
        return self::encrypt($data);
    }

    public static function verifyToken($sourceData, $token):bool {
        $payload = self::decrypt($token);
        if(empty($payload)){
            return false;
        }
        var_dump($payload);
        foreach($payload as $k => $v){
            if(in_array($k, self::$payloadParams)){
                continue;
            }
            if($v != $sourceData[$k]){
                return false;
            }
        }
        return true;
    }

    public static function encrypt($payload){
        if(!is_array($payload) || empty($payload)){
            return EzString::EMPTY;
        }
        $base64header=self::base64UrlEncode(json_encode(self::$header,JSON_UNESCAPED_UNICODE));
        $base64payload=self::base64UrlEncode(json_encode($payload,JSON_UNESCAPED_UNICODE));
        return $base64header.'.'.$base64payload.'.'.self::signature($base64header.'.'.$base64payload,self::SECRET,self::$header['alg']);
    }

    public static function decrypt($token){
        $tokens = explode('.', $token);
        if (count($tokens) != 3){
            return EzString::EMPTY;
        }
        list($base64header, $base64payload, $sign) = $tokens;

        //获取jwt算法
        $base64decodeheader = json_decode(self::base64UrlDecode($base64header), JSON_OBJECT_AS_ARRAY);
        if (empty($base64decodeheader['alg'])){
            return EzString::EMPTY;
        }

        //签名验证
        if (self::signature($base64header . '.' . $base64payload, self::SECRET, $base64decodeheader['alg']) !== $sign){
            return EzString::EMPTY;
        }

        $payload = json_decode(self::base64UrlDecode($base64payload), JSON_OBJECT_AS_ARRAY);
        return self::check($payload) ? $payload : EzString::EMPTY;
    }

    private static function check($payload):bool {
        //签发时间大于当前服务器时间验证失败
        if (isset($payload['iat']) && $payload['iat'] > time()){
            return false;
        }

        //过期时间小宇当前服务器时间验证失败
        if (isset($payload['exp']) && $payload['exp'] < time()){
            return false;
        }

        //该nbf时间之前不接收处理该Token
        if (isset($payload['nbf']) && $payload['nbf'] > time()){
            return false;
        }
        return true;
    }

    private static function signature(string $input, string $key, string $alg = 'HS256')
    {
        $alg_config=array(
            'HS256'=>'sha256'
        );
        return self::base64UrlEncode(hash_hmac($alg_config[$alg], $input, $key,true));
    }

    private static function base64UrlEncode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    private static function base64UrlDecode(string $input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $addlen = 4 - $remainder;
            $input .= str_repeat('=', $addlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }
}