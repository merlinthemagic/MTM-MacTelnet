<?php
//� 2023 Martin Peter Madsen
namespace MTM\MacTelnet\Models\Device\Mikrotik;

abstract class Alpha extends \MTM\Utilities\Tools\Validations\V1
{
	protected $_identity=null;
	protected $_licenseId=null;
	protected $_modelNbr=null;
	protected $_uptime=null;
	protected $_curVer=null;
	protected $_username=null;
	protected $_password=null;
	
	public function setIdentity($val)
	{
		$this->isStr($val, true);
		$this->_identity			= trim($val);
		return $this;
	}
	public function getIdentity()
	{
		return $this->_identity;
	}
	public function setLicenseId($val)
	{
		$this->isStr($val, true);
		$this->_licenseId		= trim($val);
		return $this;
	}
	public function getLicenseId()
	{
		return $this->_licenseId;
	}
	public function setModel($val)
	{
		$this->isStr($val, true);
		$this->_modelNbr		= trim($val);
		return $this;
	}
	public function getModel()
	{
		return $this->_modelNbr;
	}
	public function setUptime($val)
	{
		$this->isUsign32Int($val, true);
		$this->_uptime		= $val;
		return $this;
	}
	public function getUptime()
	{
		return $this->_uptime;
	}
	public function setCurrentVersion($val)
	{
		$this->isStr($val, true);
		$this->_curVer		= trim($val);
		return $this;
	}
	public function getCurrentVersion()
	{
		return $this->_curVer;
	}
	public function setUsername($val)
	{
		$this->isStr($val, true);
		$this->_username	= \MTM\Shells\Factories::getTools()->getRouterOs()->formatUsername($val);
		return $this;
	}
	public function getUsername()
	{
		return $this->_username;
	}
	public function setPassword($val)
	{
		$this->isStr($val, true);
		$this->_password	= $val;
		return $this;
	}
	public function getPassword()
	{
		return $this->_password;
	}
}