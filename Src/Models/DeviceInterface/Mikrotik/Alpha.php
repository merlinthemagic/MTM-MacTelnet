<?php
//� 2023 Martin Peter Madsen
namespace MTM\MacTelnet\Models\DeviceInterface\Mikrotik;

abstract class Alpha extends \MTM\Utilities\Tools\Validations\V1
{
	protected $_devObj=null;
	protected $_name=null;
	protected $_macAddr=null;
	
	public function setDevice($val)
	{
		$this->_devObj		= $val;
		return $this;
	}
	public function getDevice()
	{
		return $this->_devObj;
	}
	public function setName($val)
	{
		$this->isStr($val, true);
		$this->_name	= trim($val);
		return $this;
	}
	public function getName()
	{
		return $this->_name;
	}
	public function setMacAddress($macAddr)
	{
		$this->isMacAddr($macAddr, true);
		$this->_macAddr	= strtolower($macAddr);
		return $this;
	}
	public function getMacAddress()
	{
		return $this->_macAddr;
	}
}