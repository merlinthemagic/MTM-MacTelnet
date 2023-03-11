<?php
//� 2022 Martin Peter Madsen
namespace MTM\MacTelnet\Tools\Shells\RouterOs;

class Actions extends Destination
{
	public function passwordAuthenticate($ctrlObj, $macObj, $userName, $password, $timeout=30000)
	{
		return $this->passwordConnect($ctrlObj, $macObj, $userName, $password, $timeout);
	}
	public function getFormattedUsername($userName)
	{
		return \MTM\Shells\Factories::getTools()->getRouterOs()->formatUsername($userName);
	}
}