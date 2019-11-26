<?php
class EzString
{
    public const EMPTY_JSON_OBJ = "{}";

    /** 尝试把其他编码装换成utf8
     * @param $str
     * @return string
     */
    public static function convertToUnicode($str)
    {
        return self::convertToEncoding($str, 'UTF-8');
    }

    /** 尝试把其他编码装换成gbk
     * @param $str
     * @return string
     */
    public static function convertToGbk($str)
    {
        //  return mb_convert_encoding($str, 'GBK', 'auto');
        return self::convertToEncoding($str, 'GBK');
    }

    public static function convertToEncoding($str, $toEncoding)
    {
        if ((! $str) || empty($str))
        {
            return $str;
        }

        $maybechset = mb_detect_encoding($str, array('UTF-8',  'GBK', 'ASCII', 'EUC-CN',  'CP936', 'UCS-2'));
        if (empty($maybechset))
        {
            // UCS-2编码无法识别，试图猜测是否是UCS-2编码
            $tmpstr = mb_convert_encoding($str, $toEncoding, 'UCS-2');
            $tmpchset = mb_detect_encoding($tmpstr, array('GBK'));
            if (strtoupper($tmpchset) == $toEncoding)
            {
                return $tmpstr;
            }
        }
        else if ($maybechset != $toEncoding)
        { // 不是 GBK，转换一下
            return mb_convert_encoding($str, $toEncoding, $maybechset);
        }
        return $str;
    }

    /** 尝试把其他编码装换成utf8
     * @param $str
     * @return string
     */
    public static function convertToUnicodeNew($str)
    {
        if(is_bool($str) || is_int($str)) return $str;
        $encodingOrder = ['ASCII', 'CP936', 'GBK', 'UTF-8', 'EUC-CN', 'UCS-2'];
        return self::convertToEncodingNew($str, 'UTF-8', $encodingOrder);
    }

    /** 尝试把其他编码装换成gbk
     * @param $str
     * @return string
     */
    public static function convertToGbkNew($str)
    {
        if(is_bool($str) || is_int($str)) return $str;
        $encodingOrder = ['UTF-8', 'ASCII', 'CP936', 'GBK', 'EUC-CN', 'UCS-2'];
        return self::convertToEncodingNew($str, 'GBK', $encodingOrder);
    }

    protected static function convertToEncodingNew($str, $toEncoding, $recognitionArr = NULL)
    {
        if ((! $str) || empty($str))
        {
            return $str;
        }

        $encodingRecArr = ($recognitionArr === NULL) ? ['GBK', 'UTF-8'] : $recognitionArr;
        $maybechset = mb_detect_encoding($str, $encodingRecArr);
        if (empty($maybechset))
        {
            // UCS-2编码无法识别，试图猜测是否是UCS-2编码
            $tmpstr = mb_convert_encoding($str, $toEncoding, 'UCS-2');
            $tmpchset = mb_detect_encoding($tmpstr, array('GBK'));
            if (strtoupper($tmpchset) == $toEncoding)
            {
                return $tmpstr;
            }
        }
        else if ($maybechset != $toEncoding)
        {
            // 不是 GBK，转换一下
            return mb_convert_encoding($str, $toEncoding, $maybechset);
        }
        return $str;
    }

    public static function truncate($string, $length, $postfix = '...')
    {/*{{{*/
        $n = 0;
        $return = '';
        $isCode = false;	//是否是 HTML 代码
        $isHTML = false;	//是否是 HTML 特殊字符, 如&nbsp;
        for ($i = 0; $i < strlen($string); $i++)
        {
            $tmp1 = $string[$i];
            $tmp2 = ($i + 1 == strlen($string)) ? '' : $string[$i + 1];
            if ($tmp1 == '<')
            {
                $isCode = true;
            }
            elseif ($tmp1 == '&' && !$isCode)
            {
                $isHTML = true;
            }
            elseif ($tmp1 == '>' && $isCode)
            {
                $n--;
                $isCode = false;
            }
            elseif ($tmp1 == ';' && $isHTML)
            {
                $isHTML = false;
            }
            if (!$isCode && !$isHTML)
            {
                $n++;
                if (ord($tmp1) >= hexdec("0x81") && ord($tmp2) >= hexdec("0x40"))
                {
                    $tmp1 .= $tmp2;
                    $i++;
                    $n++;
                }
            }
            $return .= $tmp1;
            if ($n >= $length)
            {
                break;
            }
        }
        if ($n >= $length)
        {
            $return .= $postfix;
        }
        //取出截取字符串中的 HTML 标记
        $html = preg_replace('/(^|>)[^<>]*(<?)/', '$1$2', $return);
        //去掉不需要结束标记的 HTML 标记, 可根据情况自行更改
        $html = preg_replace("/<\/?(br|hr|img|input|param)[^<>]*\/?>/i", '', $html);
        //去掉成对的 HTML 标记
        $html = preg_replace('/<([a-zA-Z0-9]+)[^<>]*>.*?<\/\1>/', '', $html);
        //用正则表达式取出 HTML 标记
        $count = preg_match_all('/<([a-zA-Z0-9]+)[^<>]*>/', $html, $matches);
        //补全不成对的 HTML 标记
        for ($i = $count - 1; $i >= 0; $i--)
        {
            $return .= '</' . $matches[1][$i] . '>';
        }
        return $return;
    }/*}}}*/

    public static function cntrim($string)
    {
        return trim($string, "　\t\n\r ");
    }

    public static function convertEncoding($arr, $toEncoding, $fromEncoding='', $convertKey=false)
    {
        if (empty($arr) || $toEncoding == $fromEncoding)
        {
            return $arr;
        }
        if (is_array($arr))
        {
            $res = array();
            foreach ($arr as $key => $value)
            {
                if ($convertKey)
                {
                    $key = mb_convert_encoding($key, $toEncoding, $fromEncoding);
                }
                if (is_array($value))
                {
                    $value = self::convertEncoding($value, $toEncoding, $fromEncoding, $convertKey);
                }
                else
                {
                    $value = mb_convert_encoding($value, $toEncoding, $fromEncoding);
                }
                $res[$key] = $value;
            }
        }
        else
        {
            $res = mb_convert_encoding($arr, $toEncoding, $fromEncoding);
        }
        return $res;
    }

    public static function getFormatTime($time)
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        $alltime = floor((time() - $time) / 60);
        if ($alltime < 60) {
            if ($alltime <= 0) $alltime = 1;
            return $alltime . '分钟前';
        } elseif ($alltime < 60 * 24) {
            return floor($alltime / 60) . '小时前';
        } elseif ($alltime < 60 * 24 * 30) {
            return floor($alltime / 1440) . '天前';
        } else {
            return floor($alltime / 43200) . '个月前';
        }
    }

    public static function getRandom($len)
    {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        shuffle($chars);// 将数组打乱
        $output = "";
        for ($i=0; $i<$len; $i++)
        {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }

    public static function array2XML($array, $charset = 'gbk', $needCdata=true, $surRound = 'DOCUMENT')
    {
        $header = "<?xml version='1.0' encoding='".$charset."' ?>\n";
        $body = self::array2XMLBody($array, $needCdata);
        if (false == empty($surRound))
        {
            $body = "<".$surRound.">\n".$body."\n</".$surRound.">";
        }
        return $header.$body;
    }

    public static function array2XMLBody($array, $needCdata=true)
    {
        if(false == is_array($array))
        {
            return array();
        }
        $xml = "";
        foreach($array as $key=>$val)
        {
            if(is_numeric($key))
            {
                foreach( $val as $key2 => $value)
                {
                    if (false == is_numeric($key2))
                    {
                        $xml.="<$key2>";
                    }
                    if ($needCdata)
                    {
                        $xml .= is_array($value)?self::array2XMLBody($value, $needCdata):'<![CDATA['.$value.']]>'."\n";
                    }
                    else
                    {
                        $xml .= is_array($value)?self::array2XMLBody($value, $needCdata):$value."\n";
                    }
                    if (false == is_numeric($key2))
                    {
                        list($key2,)=explode(' ',$key2);
                        $xml.="</$key2>\n";
                    }
                }
            }
            else
            {
                $pre = "<$key>";
                if (is_array($val) && isset($val['@attributes']) && is_array($val['@attributes']) && false == empty($val['@attributes']))
                {
                    $pre = "<$key";
                    foreach ($val['@attributes'] as $attributeName => $attributeValue)
                    {
                        $pre .= " $attributeName='$attributeValue' ";
                    }
                    $pre .= "/>";
                    unset($val['@attributes']);
                    $key = '';
                }
                $xml.=$pre;
                if ($needCdata)
                {
                    $xml.=is_array($val)?self::array2XMLBody($val, $needCdata):'<![CDATA['.$val.']]>';
                }
                else
                {
                    $xml.=is_array($val)?self::array2XMLBody($val, $needCdata):$val;
                }
                if ($key)
                {
                    list($key,)=explode(' ',$key);
                    $xml.="</$key>\n";
                }
            }
        }

        return $xml;
    }

    public static function isEmail($email)
    {
        return false !== filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    //版本号比较 $v1:新版本号,$v2:旧版本号 返回boolean
    public static function versionCompare($v1, $v2)
    {
        if(empty($v1))
        {
            return FALSE;
        }
        $l1  = explode('.',$v1);
        $l2  = explode('.',$v2);
        $len = count($l1) < count($l2) ? count($l1): count($l2);
        for ($i = 0; $i < $len; $i++)
        {
            $n1 = $l1[$i];
            $n2 = $l2[$i];
            if ($n1 > $n2)
            {
                return TRUE;
            }
            else if ($n1 < $n2)
            {
                return FALSE;
            }
        }
        if (count($l1) > count($l2)) {
            return true;
        }
        return FALSE;

    }

    //全角转半角
    public static function fixContent2Banjiao($str)
    {
        $arr = array(
            'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
            'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
            'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
            'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
            'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
            'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
            'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
            'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
            'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
            'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
            'ｙ' => 'y', 'ｚ' => 'z', '０' => '0', '１' => '1', '２' => '2',
            '３' => '3', '４' => '4', '５' => '5', '６' => '6', '７' => '7',
            '８' => '8', '９' => '9', '　' => ' '
        );

        foreach($arr as $key => $value)
        {
            $str = mb_ereg_replace($key, $value, $str);
        }
        return $str;
    }

    /**
     * 加密电话号码
     *
     * @param string $phone
     * @static
     * @access public
     * @return string
     */
    public static function hiddenTelNumber($phone)
    {
        $kindOf = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i',$phone); //固定电话
        if ($kindOf == 1)
        {
            return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i','$1****$2',$phone);

        }
        return  preg_replace('/(1[3456789]{1}[0-9])[0-9]{5}([0-9]{2})/i','$1*****$2',$phone);
    }

    public static function hiddenEmail($email)
    {
        $hiddenStr = '';
        if (self::isEmail($email))
        {
            list($header, $footer) = explode('@', $email);
            $hiddenStr = substr($header, 0, 3)."****@".$footer;
        }
        return $hiddenStr;
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

    public static function encodeJson($obj){
        return json_encode($obj) ?? self::EMPTY_JSON_OBJ;
    }
}
