<?php
class DynamicProxy
{
    /*
    *   不传系统名则创建proxy实例，使用第三方接口
    *   传入系统名，先判断是否能找到service类，能找到直接调用，
    *       否则查看独立部署服务列表
    *   除以上情况抛出异常
    */
	public static function getInstance($svcCode = '')
    {/*{{{*/
        if(!empty(Loadder::getSvc($svcCode)))
        {/*{{{*/
			return Loadder::getSvc($svcCode);
		}/*}}}*/
		$sysService = $svcCode.'Service';
		if(class_exists($sysService))
        {
			$svc = new $sysService;
		}
        else
        {
			$svc = new Remoter($svcCode);
		}
		Loadder::setSvc($svcCode, $svc);
		return Loadder::getSvc($svcCode);
	}/*}}}*/
}
