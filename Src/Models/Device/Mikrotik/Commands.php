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
	public function setCmdReboot()
	{
		//required for 6 -> 7 net-install upgrades
		if (strpos($this->getCurrentVersion(), "7") === 0) {
			$strCmd			= "/system/reboot;";
		} else {
			$strCmd			= "/system reboot;";
		}
		
		$regEx			= "Reboot, yes\? \[y\/N\]\:";
		$this->getCtrl()->getCmd($strCmd, $regEx)->exec()->get(true);
		
		$strCmd			= "y";
		$regEx			= "system will reboot shortly";
		$this->getCtrl()->getCmd($strCmd, $regEx)->exec()->get(true);
		try {
			$this->_ctrlObj->terminate();
		} catch (\Exception $e) {
		}
		$this->_ctrlObj	= null;
		return $this;
	}
	public function setCmdBootDeviceOpt($opt)
	{
		//required for 6 -> 7 net-install upgrades
		//e.g. try-ethernet-once-then-nand
		if (strpos($this->getCurrentVersion(), "7") === 0) {
			$strCmd		= "/system/routerboard/settings/set boot-device=\"".$opt."\";";
		} else {
			$strCmd		= "/system routerboard settings set boot-device=\"".$opt."\";";
		}
		return trim($this->getCmdReturn($strCmd, true));
	}
	public function getCmdBootDeviceOpt()
	{
		$strCmd		= ":put [/system/routerboard/settings/get boot-device];";
		return trim($this->getCmdReturn($strCmd, true));
	}
	public function getCmdArchitecture()
	{
		//required for 6 -> 7 net-install upgrades
		if (strpos($this->getCurrentVersion(), "7") === 0) {
			$strCmd		= ":put [/system/resource/get architecture-name];";
		} else {
			$strCmd		= ":put [/system resource get architecture-name];";
		}
		return trim($this->getCmdReturn($strCmd, true));
	}
	public function setCmdIdentity($name)
	{
		//changing the identity of the device will break the delimitor on the shell
		$strCmd		= "/system/identity/set name=\"".$name."\";";
		$this->_ctrlObj->getCmd($strCmd, false, 250)->exec()->get(false);
		$this->_ctrlObj->resetDefaultRegEx();
		
		$strCmd		= ":put [/system/identity/get name];";
		return trim($this->getCmdReturn($strCmd, true));
	}
	public function setCmdUpgrade()
	{
		$strCmd			= "/system/routerboard/upgrade";
		$regEx			= "Do you really want to upgrade firmware\? \[y\/n\]";
		$this->getCtrl()->getCmd($strCmd, $regEx)->exec()->get(true);
		
		$strCmd			= "y";
		$this->getCtrl()->getCmd($strCmd)->exec()->get(true);
		return $this;
	}
	public function setCmdQuit()
	{
		if ($this->_ctrlObj !== null) {
			$strCmd			= "/quit";
			$this->_ctrlObj->getCmd($strCmd, false, 100)->exec()->get(false);
			try {
				$this->_ctrlObj->terminate();
			} catch (\Exception $e) {
			}
			$this->_ctrlObj	= null;
		}
		return $this;
	}
}