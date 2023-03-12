<?php
//� 2023 Martin Peter Madsen
namespace MTM\MacTelnet\Facts;

class Devices extends Base
{
	public function getMikrotik()
	{
		$rObj	= new \MTM\MacTelnet\Models\Device\Mikrotik\Zulu();
		return $rObj;
	}
}