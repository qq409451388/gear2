<?php
class EzCurl implements IProxy
{ 
    private $ch;  
    private $url;
    private $haveRun;   //标记exec是否已经运行
    private $setTimeOut = 5;  //设置curl超时时间
    private $cookieFile = "";  //cookieFile路径
    private $cookieMode = 0;    //cookie保存模式 0不使用 1客户端、2服务器文件
    private $showHeader = 0;    //是否输出返回头信息
    private $userAgent = ""; //模拟用户使用的浏览器，默认为模拟

    //构造函数  
    public function __construct()
    {  /*{{{*/
        $this->init();
    }  /*}}}*/

    private function init()
    {/*{{{*/
        $this->ch = curl_init();  
        $this->setUserAgent();
        $this->initCookieFile();
    }/*}}}*/

    public function setUrl($url)
    {/*{{{*/
        $this->url = $url;    
        return $this;
    }/*}}}*/

    private function initCookieFile()
    {/*{{{*/
        //$this->cookieFile=dirname(__FILE__)."/cookie_".md5(basename(__FILE__)).".txt";    //初始化cookie文件路径 
        //$this->cookieFile= SAE_TMP_PATH.TmpFS;
        $this->cookieFile = "saekv://cookie_2014.txt";
        return $this;
    }/*}}}*/

   //设置超时   
    public function setTimeOut($timeout=5)
    {  /*{{{*/
        if(intval($timeout) != 0)       
        $this->setTimeOut = $timeout;
        return $this;
    }  /*}}}*/

    //设置来源页面  
    public function setReferer($referer = "")
    { /*{{{*/
        if (!empty($referer))  
            curl_setopt($this->ch, CURLOPT_REFERER , $referer);  
        return $this;  
    }/*}}}*/

    //设置cookie存放模式 1客户端、2服务器文件  
    public function setCookieMode($mode = "")
    { /*{{{*/
        $this->cookieMode = $mode;
        return $this;
    }/*}}}*/

    //载入cookie  
    public function loadCookie()
    { /*{{{*/
        if($this->cookieMode == 1 ) 
        {
            if(isset($_COOKIE['curl'])){
                curl_setopt($this->ch,CURLOPT_COOKIE,$_COOKIE['curl']);
            }else{
                $this->exec();
                curl_setopt($this->ch,CURLOPT_COOKIE,$this->cookieFile);
            }

        }
        if($this->cookieMode == 2 ) 
        {

            curl_setopt($this->ch, CURLOPT_COOKIEFILE , $this->cookieFile);

        }
        if($this->cookieMode == 3 ) 
        {
            $kv = new SaeKV();
            $ret = $kv->init();
            $ret = $kv->get('curl_cookie');
            if($ret)
               curl_setopt($this->ch,CURLOPT_COOKIE, $ret);

        }
        return $this;  
    }  /*}}}*/

    //设置保存cookie方式 $cookie_val 模式1为变量 模式2为文件路径
    public function saveCookie($cookie_val = "") 
    {/*{{{*/
        //保存在客户端
        if($this->cookieMode == 1 && $cookie_val)
        {
           setcookie('curl',$cookie_val); 
        }
        //保存服务器端
        if($this->cookieMode == 2)
        { 
            if(!empty($cookie_val))  
               $this->cookieFile =  $cookie_val;
            curl_setopt($this->ch, CURLOPT_COOKIEJAR , $this->cookieFile);  
        }
        //保存在sae
        if($this->cookieMode == 3 && $cookie_val)
        {
            $kv = new SaeKV();
            $ret = $kv->init();
            $ret = $kv->get('curl_cookie');
            if($ret)
            {
                $ret = $kv->set('curl_cookie', $cookie_val );
            }
            else
            {
                $ret = $kv->add('curl_cookie', $cookie_val);
            }
        }

        return $this;  
    }/*}}}*/

    //post参数 (array) $post 
    public function post (Array $post = [])
    {  /*{{{*/
        if(!empty($post))
        {
            curl_setopt($this->ch, CURLOPT_SAFE_UPLOAD, false);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->ch, CURLOPT_POST , 1);  
            curl_setopt($this->ch, CURLOPT_POSTFIELDS , $post);  
        }
        return $this;  
    }  /*}}}*/

    public function get(Array $get = [])
    {/*{{{*/
        if(!empty($get))
        {
            $url = $this->url.'?'.http_build_query($get);
            $this->setUrl($url);
        }
        return $this; 
    }/*}}}*/

    //设置代理 ,例如'68.119.83.81:27977'  
    public function setProxy($ip = "", $port = 80)
    {/*{{{*/
        $proxy = $ip.':'.$port;
        if($proxy)
        { 
            curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);  
            curl_setopt($this->ch, CURLOPT_PROXY,$proxy); 
        }           
        return $this;  
    }  /*}}}*/

    //设置伪造ip  
    public function setIp($ip="")
    { /*{{{*/
        if(!empty($ip))  
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("X-FORWARDED-FOR:$ip", "CLIENT-IP:$ip"));  
        return $this;  
    } /*}}}*/

     //设置是否显示返回头信息
    public function setShowHeader($show=0)
    {/*{{{*/
        $this->showHeader = 0;  
        if($show) 
            $this->showHeader = 1; 
        return $this;  
    }/*}}}*/

     //设置请求头信息
    public function setUserAgent($str="")
    { /*{{{*/
        if($str)  
        {
            $this->userAgent = $str;  
        }
        else
        {
            $this->userAgent = $_SERVER['HTTP_USER_AGENT']; 
        }
        return $this;
    }   /*}}}*/

    public function setFromApple()
    {/*{{{*/
        return $this->setUserAgent("Mozilla/5.0 (iPhone; CPU iPhone OS 6_1_4 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/7.0 Mobile/10B350 Safari/9537.53");
    }/*}}}*/

    public function setFromChrome()
    {/*{{{*/
        return $this->setUserAgent("Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.143 Safari/537.36");
    }/*}}}*/

    //执行  
    public function exec ()
    { /*{{{*/
        curl_setopt($this->ch, CURLOPT_URL, $this->url); // 要访问的地址
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查   
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER , 1 );    //获取的信息以文件流的形式返回     
        //curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在 
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent); // 模拟用户使用的浏览器      
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转      
        curl_setopt($this->ch, CURLOPT_AUTOREFERER, 1); // 自动设置Referer 
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->setTimeOut);  //超时设置
        curl_setopt($this->ch, CURLOPT_HEADER, $this->showHeader); // 显示返回的Header区域内容
        curl_setopt($this->ch, CURLOPT_NOBODY, 0);//不返回response body内容 

        $res = curl_exec($this->ch);
        $this->haveRun = true;
        if (curl_errno($this->ch)) 
        { 
            Assert::exception('【Proxy Exception】Errno'.curl_error($this->ch));
        } 
        if($this->showHeader == 1)
        { 
            //数组形式返回头信息和body信息 
            list($header, $body) = explode("\r\n\r\n", $res);
            $arr['header'] = $header;
            $arr['body'] = $body;
            if($this->cookieMode == 1 || $this->cookieMode == 3)
            {  
                preg_match_all("/set\-cookie:([^\r\n]*)/i", $header, $matches);
                //print_r($matches);
                if($matches && isset($matches[1]))
                {
                    $val = implode(';',array_unique(explode(';',implode(';',$matches[1])))); //去重处理
                    if($val)
                      $this->saveCookie($val); //设置客户端保存cookie
                }
            }
            if($arr) 
                return $arr;
        }

        return $res;  
    }  /*}}}*/

    //返回  curl_getinfo信息
    public function getInfo()
    {  /*{{{*/
        if($this->haveRun)  
            return curl_getinfo($this->ch);  
        else  
            Assert::exception("<h1>需先运行( 执行exec )，再获取信息</h1>");  
    }  /*}}}*/

    //关闭curl
    public function close()
    {  /*{{{*/
        curl_close($this->ch);  
    }  /*}}}*/

    //析构函数  
    public function __destruct()
    { /*{{{*/
        $this->close();  
    }  /*}}}*/

}  
