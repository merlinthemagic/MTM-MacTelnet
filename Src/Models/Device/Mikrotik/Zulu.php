<?php
//� 2023 Martin Peter Madsen
namespace MTM\MacTelnet\Models\Device\Mikrotik;

class Zulu extends Interfaces
{
	public function terminate()
	{
		if ($this->_ctrlObj !== null) {
			$ctrlObj			= $this->_ctrlObj;
			$this->_ctrlObj		= null;
			$ctrlObj->terminate();
		}
	}
}