<?php
class Config{
	private $map = [];

	public function __construct($config){
	    if(!is_dir($config)){
	        Assert::argEx("[Config] is not dir $config");
        }
    }

    public function getAll(){
		return $this->map;
	} 

	public function get($type){
		return empty($this->map[$type]) ? null : $this->map[$type];
	}

    public function getConfig($a)
    {
        return $this->get($a);    
    }

	public function put($configs){
        $path = EnvSetup::getConfigPath();
		foreach($configs as $config){
			$configPath = $path.'/'.$config;
			if(is_file($configPath)){
				$info = file_get_contents($configPath);
				$this->setConfig(str_replace('.json', '', $config), json_decode($info));
			}
		}
	}

	public function setConfig($type, $config){
		$this->map[$type] = $config;
	}
}
