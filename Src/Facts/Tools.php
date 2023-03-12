<?php
//� 2023 Martin Peter Madsen
namespace MTM\MacTelnet\Facts;

class Tools extends Base
{
	public function getMacTelnet()
	{
		if (array_key_exists(__FUNCTION__, $this->_s) === false) {
			$this->_s[__FUNCTION__]		= new \MTM\MacTelnet\Tools\MacTelnet\Zulu();
		}
		return $this->_s[__FUNCTION__];
	}
}