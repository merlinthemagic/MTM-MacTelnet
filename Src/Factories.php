<?php
//© 2022 Martin Peter Madsen
namespace MTM\MacTelnet;

class Factories
{
	private static $_cStore=array();
	
	//USE: $aFact		= \MTM\MacTelnet\Factories::$METHOD_NAME();
	
	public static function getShells()
	{
		if (array_key_exists(__FUNCTION__, self::$_cStore) === false) {
			self::$_cStore[__FUNCTION__]	= new \MTM\MacTelnet\Factories\Shells();
		}
		return self::$_cStore[__FUNCTION__];
	}
}