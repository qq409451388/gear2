<?php
class EzCurl
{ 
    private $ch;  
    private $url;
    private $haveRun;   //���exec�Ƿ��Ѿ�����
    private $setTimeOut = 5;  //����curl��ʱʱ��
    private $cookieFile = "";  //cookieFile·��
    private $cookieMode = 0;    //cookie����ģʽ 0��ʹ�� 1�ͻ��ˡ�2�������ļ�
    private $showHeader = 0;    //�Ƿ��������ͷ��Ϣ
    private $userAgent = ""; //ģ���û�ʹ�õ��������Ĭ��Ϊģ��

    //���캯��  
    public function __construct()
    {  /*{{{*/
        $this->init();
    }  /*}}}*/

    private function init()
    {/*{{{*/
        $this->ch = curl_init();
        $this->trace = new Trace();
        $this->setUserAgent();
        $this->initCookieFile();
    }/*}}}*/

    private function initCookieFile()
    {/*{{{*/
        //$this->cookieFile=dirname(__FILE__)."/cookie_".md5(basename(__FILE__)).".txt";    //��ʼ��cookie�ļ�·��
        //$this->cookieFile= SAE_TMP_PATH.TmpFS;
        $this->cookieFile = "saekv://cookie_2014.txt";
        return $this;
    }/*}}}*/

    public function setUrl($url)
    {/*{{{*/
        $this->url = $url;    
        return $this;
    }/*}}}*/

    //post���� (array) $post
    public function post (Array $post = [])
    {  /*{{{*/
        if(!empty($post))
        {
            curl_setopt($this->ch, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->ch, CURLOPT_POST , 1);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS , $post);
        }
        return $this->exec();
    }  /*}}}*/

    public function get(Array $get = [])
    {/*{{{*/
        if(!empty($get))
        {
            $url = $this->url.'?'.http_build_query($get);
            $this->setUrl($url);
        }
        return $this->exec();
    }/*}}}*/

   //���ó�ʱ   
    public function setTimeOut($timeout=5)
    {  /*{{{*/
        if(intval($timeout) != 0)       
        $this->setTimeOut = $timeout;
        return $this;
    }  /*}}}*/

    //������Դҳ��  
    public function setReferer($referer = "")
    { /*{{{*/
        if (!empty($referer))  
            curl_setopt($this->ch, CURLOPT_REFERER , $referer);  
        return $this;  
    }/*}}}*/

    //����cookie���ģʽ 1�ͻ��ˡ�2�������ļ�  
    public function setCookieMode($mode = "")
    { /*{{{*/
        $this->cookieMode = $mode;
        return $this;
    }/*}}}*/

    //����cookie  
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
        return $this;  
    }  /*}}}*/

    //���ñ���cookie��ʽ $cookie_val ģʽ1Ϊ���� ģʽ2Ϊ�ļ�·��
    public function saveCookie($cookie_val = "") 
    {/*{{{*/
        //�����ڿͻ���
        if($this->cookieMode == 1 && $cookie_val)
        {
           setcookie('curl',$cookie_val); 
        }
        //�����������
        if($this->cookieMode == 2)
        { 
            if(!empty($cookie_val))  
               $this->cookieFile =  $cookie_val;
            curl_setopt($this->ch, CURLOPT_COOKIEJAR , $this->cookieFile);  
        }

        return $this;  
    }/*}}}*/

    //���ô��� ,����'68.119.83.81:27977'  
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

    //����α��ip  
    public function setIp($ip="")
    { /*{{{*/
        if(!empty($ip))  
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("X-FORWARDED-FOR:$ip", "CLIENT-IP:$ip"));  
        return $this;  
    } /*}}}*/

     //�����Ƿ���ʾ����ͷ��Ϣ
    public function setShowHeader(bool $show=false)
    {/*{{{*/
        $this->showHeader = (int)$show;
        return $this;
    }/*}}}*/

     //��������ͷ��Ϣ
    public function setUserAgent($str="")
    { /*{{{*/
        if($str)  
        {
            $this->userAgent = $str;  
        }
        else
        {
            $this->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
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

    //ִ��  
    private function exec()
    { /*{{{*/
        $this->trace->start();
        curl_setopt($this->ch, CURLOPT_URL, $this->url); // Ҫ���ʵĵ�ַ
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0); // ����֤֤����Դ�ļ��   
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER , 1 );    //��ȡ����Ϣ���ļ�������ʽ����     
        //curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 1); // ��֤���м��SSL�����㷨�Ƿ���� 
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent); // ģ���û�ʹ�õ������      
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1); // ʹ���Զ���ת      
        curl_setopt($this->ch, CURLOPT_AUTOREFERER, 1); // �Զ�����Referer 
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->setTimeOut);  //��ʱ����
        curl_setopt($this->ch, CURLOPT_HEADER, $this->showHeader); // ��ʾ���ص�Header��������
        curl_setopt($this->ch, CURLOPT_NOBODY, 0);//������response body���� 

        $res = curl_exec($this->ch);
        $msg = 'EzCurl '.$this->url;
        $this->trace->log($msg, __CLASS__);
        $this->haveRun = true;
        if (curl_errno($this->ch)) 
        { 
            DBC::throwEx('��EzCurl Exception��Proxy Errno'.curl_error($this->ch));
        } 
        if($this->showHeader == 1)
        { 
            //������ʽ����ͷ��Ϣ��body��Ϣ 
            list($header, $body) = explode("\r\n\r\n", $res);
            $arr['header'] = $header;
            $arr['body'] = $body;
            if($this->cookieMode == 1 || $this->cookieMode == 3)
            {  
                preg_match_all("/set\-cookie:([^\r\n]*)/i", $header, $matches);
                //print_r($matches);
                if($matches && isset($matches[1]))
                {
                    $val = implode(';',array_unique(explode(';',implode(';',$matches[1])))); //ȥ�ش���
                    if($val)
                      $this->saveCookie($val); //���ÿͻ��˱���cookie
                }
            }
            if($arr) 
                return $arr;
        }

        return $res;  
    }  /*}}}*/

    //����  curl_getinfo��Ϣ
    public function getInfo()
    {  /*{{{*/
        if($this->haveRun)  
            return curl_getinfo($this->ch);  
        else  
            DBC::throwEx("��EzCurl Exception������ִ��get/post����");
    }  /*}}}*/

    //�ر�curl
    private function close()
    {  /*{{{*/
        if(!is_null($this->ch))
        {
            curl_close($this->ch);
        }
    }  /*}}}*/

    //��������  
    public function __destruct()
    { /*{{{*/
        $this->close();  
    }  /*}}}*/

}  
