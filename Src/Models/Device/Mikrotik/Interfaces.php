<?php
//� 2023 Martin Peter Madsen
namespace MTM\MacTelnet\Models\Device\Mikrotik;

abstract class Interfaces extends Commands
{
	protected $_ifObjs=array();

	public function addInterface($name, $macAddr)
	{
		$this->isStr($name, true);
		$this->isMacAddr($macAddr, true);
		$name	= trim($name);
		if (array_key_exists($name, $this->_ifObjs) === false) {
			$ifObj					= new \MTM\MacTelnet\Models\DeviceInterface\Mikrotik\Zulu();
			$ifObj->setDevice($this)->setName($name)->setMacAddress($macAddr);
			$this->_ifObjs[$name]			= $ifObj;
		}
		return $this->_ifObjs[$name];
	}
	public function getInterfaces()
	{
		return array_values($this->_ifObjs);
	}
}