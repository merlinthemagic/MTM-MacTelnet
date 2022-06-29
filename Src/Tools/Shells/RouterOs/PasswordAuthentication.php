<?php
//© 2022 Martin Peter Madsen
namespace MTM\MacTelnet\Tools\Shells\RouterOs;

class PasswordAuthentication extends \MTM\MacTelnet\Tools\Shells\Base
{
	protected function passwordConnect($ctrlObj, $macObj, $userName, $password, $timeout=30000)
	{
		if ($ctrlObj->getType() == "routeros") {
			return $this->passwordConnectFromRouterOs($ctrlObj, $macObj, $userName, $password, $timeout);
		} else {
			throw new \Exception("Not handled");
		}
	}
	protected function passwordConnectFromRouterOs($ctrlObj, $macObj, $userName, $password, $timeout=30000)
	{
		$strCmd		= "/tool/mac-telnet";
		$strCmd		.= " host=\"".$macObj->getAsString("std", false) . "\"";

		$regEx		= "Login\:";
		$data		= $ctrlObj->getCmd($strCmd, $regEx, $timeout)->get();
		
		$strCmd		= $userName;
		$regEx		= "Password\:";
		$data		= $ctrlObj->getCmd($strCmd, $regEx, $timeout)->get();
		
		$rawUser	= $userName;
		if (preg_match("/(.+?)\+ct1000w1000h$/", $userName, $raw) === 1) {
			//mikrotik formatted username
			$rawUser	= $raw[1];
		}
		$regExs	= array(
				"\[".$rawUser."\@(.+?)\] \>(\s+)?$"					=> "routeros",
				"Login failed, incorrect username or password"		=> "error",
				"Welcome back!"										=> "timeout" //timeout
		);

		$strCmd		= $password;
		$regEx		= "(" . implode("|", array_keys($regExs)) . ")";
		$cmdObj		= $ctrlObj->getCmd($strCmd, $regEx, $timeout);
		$cmdObj->get();
		$data		= $cmdObj->getReturnData(); //need return data so the prompt is not stripped out
		
		$rType	= null;
		foreach ($regExs as $regEx => $type) {
			if (preg_match("/".$regEx."/", $data) == 1) {
				$rValue	= $regEx;
				$rType	= $type;
				break;
			}
		}
		if ($rType == "routeros") {
			
			if ($rValue == "Do you want to see the software license") {
				//we are the only ones with the information needed to clear the prompt
				//if we dont clear it here the Destination function will have a hell of a time figuring out whats going on
				$strCmd	= "n";
				$regEx	= "(" . implode("|", array_keys($regExs)) . ")";
				$ctrlObj->getCmd($strCmd, $regEx, $timeout)->get();
			}

			return $this->getDestinationShell($ctrlObj, $userName);
			
		} elseif ($rType == "error") {
			throw new \Exception("Connect error: " . $rValue);
		} elseif ($rType == "timeout") {
			throw new \Exception("Connection timed out");
		} else {
			throw new \Exception("Not Handled: " . $rType);
		}
	}
}