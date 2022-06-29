<?php
//© 2022 Martin Peter Madsen
namespace MTM\MacTelnet\Tools\Shells\RouterOs;

class Actions extends Destination
{
	public function passwordAuthenticate($ctrlObj, $macObj, $userName, $password, $timeout=30000)
	{
		return $this->passwordConnect($ctrlObj, $macObj, $userName, $password, $timeout);
	}
	public function getFormattedUsername($userName)
	{
		//default terminal options for all Mikrotik MacTelnet connections.
		//We need the terminal without colors and a standard width / height
		$rosOpts	= "ct1000w1000h";
		if (strpos($userName, "+") !== false) {
			if (preg_match("/(.*?)\+(.*)/", $userName, $raw) == 1) {
				//username has options, but they may be the wrong ones
				$userName	= $raw[1] . "+" . $rosOpts;
			} else {
				throw new \Exception("Not handled");
			}
		} else {
			$userName	.= "+" . $rosOpts;
		}
		return $userName;
	}
}