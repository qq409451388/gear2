<?php
class Remoter
{
    private $proxy;
    private $sysName;

    public function __construct($sysName = '')
    {
        $this->init($sysName);
    }

    private function init($sysName)
    {
        if(!empty($sysName))
        {
            $systemConfigs = Loadder::get('Config')->get('system');
            if(empty($systemConfigs->$sysName))
            {
                Assert::exception('【Remoter Exception】不存在的系统'.$sysName);    
            }
            $this->config = $systemConfigs->$sysName;
        }
        $this->proxy = new EzCurl();    
        $this->sysName = $sysName;
    }

    public function getHost()
    {
        return EnvSetup::isDev() ? '127.0.0.1' : $this->config->host;
    }

    public function getPort()
    {
        return EnvSetup::isDev() ? 9001 : $this->config->port;
    }

    public function putUrl($url)
    {
        if(!empty($this->sysName))
        {
            $queryArr = explode('/', $url);
            $newQueryArr = [
                'svc' => $queryArr[0],
                'method' => $queryArr[1]
            ];
            $query = http_build_query($newQueryArr);
            $url = $this->getHost().':'.$this->getPort().'?init.php&'.$query;
        }
        $this->proxy->setUrl($url);
        return $this; 
    }

    public function post($url, $params = [])
    {
        $this->putUrl($url);
        return $this->proxy->post($params)->exec(); 
    }

    public function get($url, $params = [])
    {
        $this->putUrl($url);
        return $this->proxy->get($params)->exec();    
    }

}
