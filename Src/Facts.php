<?php
//� 2022 Martin Peter Madsen
namespace MTM\MacTelnet;

class Facts
{
	private static $_s=array();
	
	//USE: $aFact		= \MTM\MacTelnet\Facts::$METHOD_NAME();
	
	public static function getShells()
	{
		if (array_key_exists(__FUNCTION__, self::$_s) === false) {
			self::$_s[__FUNCTION__]	= new \MTM\MacTelnet\Facts\Shells();
		}
		return self::$_s[__FUNCTION__];
	}
	public static function getTools()
	{
		if (array_key_exists(__FUNCTION__, self::$_s) === false) {
			self::$_s[__FUNCTION__]	= new \MTM\MacTelnet\Facts\Tools();
		}
		return self::$_s[__FUNCTION__];
	}
	public static function getDevices()
	{
		if (array_key_exists(__FUNCTION__, self::$_s) === false) {
			self::$_s[__FUNCTION__]	= new \MTM\MacTelnet\Facts\Devices();
		}
		return self::$_s[__FUNCTION__];
	}
}