<?php
//� 2023 Martin Peter Madsen
namespace MTM\MacTelnet\Models\Device\Mikrotik;

abstract class Commands extends Alpha
{
	protected $_ctrlObj=null;
	
	public function setCtrlByInterface($ifObj)
	{
		$this->_ctrlObj		= \MTM\MacTelnet\Facts::getShells()->passwordAuthentication($ifObj->getMacAddress(), $this->getUsername(), $this->getPassword());
		return $this;
	}
	public function getCtrl()
	{
		if ($this->_ctrlObj === null) {
			//pick the first interface, not sure its allowed, but if you want to be specific, you choose
			$ifObjs		= $this->getInterfaces();
			$this->setCtrlByInterface(reset($ifObjs));
		}
		return $this->_ctrlObj;
	}
	public function getCmdReturn($strCmd, $throw=true)
	{
		return $this->getCtrl()->getCmd($strCmd)->exec()->get($throw);
	}
	public function getCmdBootDeviceOpt()
	{
		$strCmd		= ":put [/system/routerboard/settings/get boot-device];";
		return trim($this->getCmdReturn($strCmd, true));
	}
}