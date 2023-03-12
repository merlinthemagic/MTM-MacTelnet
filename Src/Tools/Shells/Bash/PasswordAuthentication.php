<?php
//� 2022 Martin Peter Madsen
namespace MTM\MacTelnet\Tools\Shells\Bash;

class PasswordAuthentication extends \MTM\MacTelnet\Tools\Shells\Base
{
	protected function passwordConnect($ctrlObj, $macObj, $userName, $password, $timeout=30000)
	{
		if ($ctrlObj->getType() == "bash") {
			return $this->passwordConnectFromBash($ctrlObj, $macObj, $userName, $password, $timeout);
		} else {
			throw new \Exception("Not handled");
		}
	}
	protected function passwordConnectFromBash($ctrlObj, $macObj, $userName, $password, $timeout=30000)
	{
		$mtPath		= "mactelnet";
		if ($ctrlObj->getParent() === null) {
			try {
				$mtPath		= \MTM\MacTelnet\Facts::getTools()->getMacTelnet()->getMacTelnetPath();
			} catch (\Exception $e) {
				//go with the default, hopefully its installed
			}
		}

		$baseCmd	= $mtPath." -A ".$macObj->getAsString("std", false);
		$regExs		= array(
				"Login\:"									=> "success",
				"No such file or directory"					=> "Missing MacTelnet application"
		);
		
		$regEx		= "(" . implode("|", array_keys($regExs)) . ")";
		$data		= $ctrlObj->getCmd($baseCmd, $regEx, $timeout)->get();
		
		foreach ($regExs as $regEx => $msg) {
			if (preg_match("/".$regEx."/", $data) == 1) {
				if ($msg !== "success") {
					throw new \Exception("Connect error: '" . $msg."'");
				}
				break;
			}
		}
		
		$strCmd		= $userName;
		$regEx		= "Password\:";
		$data		= $ctrlObj->getCmd($strCmd, $regEx, $timeout)->get();

		$rawUser	= $userName;
		if (preg_match("/(.+?)\+ct1000w1000h$/", $userName, $raw) === 1) {
			//mikrotik formatted username
			$rawUser	= $raw[1];
		}
		$regExs	= array(
				"new password\>"									=> "routeros",
				"\[".$rawUser."\@(.+?)\] \>(\s+)?$"					=> "routeros",
				"Do you want to see the software license\?"			=> "routeros",
				"remove it, you will be disconnected\."				=> "routeros",
				"Login failed, incorrect username or password"		=> "invalid",
				"Welcome back!"										=> "timeout", //timeout
				"Invalid salt length\:"								=> "invalid"
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
			if ($rValue == "remove it, you will be disconnected\.") {
				//we are the only ones with the information needed to clear the prompt
				//if we dont clear it here the Destination function will have a hell of a time figuring out whats going on
				$strCmd	= "n";
				$regEx	= "(" . implode("|", array_keys($regExs)) . ")";
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
			}
			
			if ($rValue == "Do you want to see the software license\?") {
				//we are the only ones with the information needed to clear the prompt
				//if we dont clear it here the Destination function will have a hell of a time figuring out whats going on
				$strCmd	= "n";
				$regEx	= "(" . implode("|", array_keys($regExs)) . ")";
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
			}
			//there can be both a license and a forced password change one after the other
			if ($rValue == "new password\>") {
				//MT forcing password change, deny the change
				$strCmd		= chr(3);
				$regEx	= "(" . implode("|", array_keys($regExs)) . ")";
				$ctrlObj->getCmd($strCmd, $regEx, $timeout)->get();
			}

			return $this->getDestinationShell($ctrlObj, $userName);
			
		}
		
		if ($rType == "error") {
			throw new \Exception("Connect error: '" . $rValue."'");
		} elseif ($rType == "timeout") {
			throw new \Exception("Connection timed out");
		} elseif ($rType == "invalid") {
			//might just be a alpha release issue, but this blocks the connection indefinetly (unless #83 is merged) 
			//raised in issue: https://github.com/haakonnessjoen/MAC-Telnet/issues/82
			//added pull request: https://github.com/haakonnessjoen/MAC-Telnet/pull/83
			throw new \Exception("Connect error: 'Login failed, incorrect username'", 88236); //used to determine if the default admin / "" is not valid
		} else {
			throw new \Exception("Not Handled for type: " . $rType);
		}
	}
}