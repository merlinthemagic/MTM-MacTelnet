<?php
//� 2022 Martin Peter Madsen
namespace MTM\MacTelnet\Tools\Shells\Bash;

class Destination extends PasswordAuthentication
{
	public function getDestinationShell($ctrlObj, $userName) 
	{
		if ($this->getFormattedUsername($userName) != $userName) {
			//usernames must be formatted correctly when connecting to RouterOs
			//we cannot know wht we are connecting to ahead of time, so its up to you
			//to pass the username through the following function before connecting to routerOS
			//use: \MTM\MacTelnet\Factories::getShells()->getRouterOsTool()->getFormattedUsername($userName);
			throw new \Exception("Username is invalid for RouterOs");
		}
		$childObj	= \MTM\MacTelnet\Facts::getShells()->getRouterOs();
		$childObj->setParent($ctrlObj);
		$ctrlObj->setChild($childObj);
		//init the shell, we are already logged in
		$childObj->getCmd(":put MTM;")->get();
		$childObj->resetPrompt();
		return $childObj;
	}
}