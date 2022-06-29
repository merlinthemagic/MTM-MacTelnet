<?php
//© 2022 Martin Peter Madsen
namespace MTM\MacTelnet\Factories;

class Shells extends Base
{
	public function passwordAuthentication($macAddr, $user, $pass, $ctrlObj=null, $timeout=30000)
	{
		try {
			//generic password authentication
			$newBase	= false;
			if ($macAddr instanceof \MTM\Network\Models\Mac\EUI48 === false) {
				$macAddr		= \MTM\Network\Factories::getMac()->getEui48($macAddr);
			}
			if ($ctrlObj === null) {
				$newBase	= true;
				$ctrlObj	= $this->getBaseShell();
			}
			return $this->getTool($ctrlObj)->passwordAuthenticate($ctrlObj, $macAddr, $user, $pass, $timeout);
			
		} catch (\Exception $e) {
			if ($newBase === true && is_object($ctrlObj) === true) {
				$ctrlObj->terminate();
			}
			throw $e;
		}
	}
	public function getRouterOs()
	{
		$rObj	= new \MTM\MacTelnet\Models\Shells\RouterOs\Actions();
		return $rObj;
	}
	public function getTool($ctrlObj)
	{
		if ($ctrlObj->getType() == "routeros") {
			return $this->getRouterOsTool();
		} else {
			throw new Exception("Hot handled for shell type: ".$ctrlObj->getType());
		}
	}
	public function getRouterOsTool()
	{
		if (array_key_exists(__FUNCTION__, $this->_s) === false) {
			$this->_s[__FUNCTION__]		= new \MTM\MacTelnet\Tools\Shells\RouterOs\Actions();
		}
		return $this->_s[__FUNCTION__];
	}
	protected function getBaseShell()
	{
		throw new Exception("Current Linux based Mac telnet client does not support authentication for routerOS");
		$osTool		= \MTM\Utilities\Factories::getSoftware()->getOsTool();
		if ($osTool->getType() == "linux") {
			$ctrlObj	= \MTM\Shells\Factories::getShells()->getBash();
			
			//trap the MacTelnet conn exit, that way the user cannot drop into a local shell they did not create
			$strCmd		= "trap \"trap - SIGCHLD; echo mtm-forced-exit; exit\" SIGCHLD";
			$ctrlObj->getCmd($strCmd)->get();
			return $ctrlObj;
			
		} else {
			throw new \Exception("Not handled");
		}
	}
}