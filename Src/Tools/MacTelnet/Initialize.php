<?php
//� 2023 Martin Peter Madsen
namespace MTM\MacTelnet\Tools\MacTelnet;

abstract class Initialize extends Discover
{
	protected $_mtPath=null;
	
	public function getMacTelnetPath()
	{
		if ($this->_mtPath === null) {
			
			$osTool		= \MTM\Utilities\Factories::getSoftware()->getOsTool();
			if ($osTool->getType() === "linux") {
				$basePath	= MTM_MAC_TELNET_BASE_PATH."Resources".DIRECTORY_SEPARATOR."MacTelnet".DIRECTORY_SEPARATOR;
				$distId		= $osTool->getDistributionId();
				$archId		= $osTool->getArchitecture();
				if ($distId === "ubuntu" && $archId === "arm64") {
					$this->_mtPath	= $basePath."Ubuntu".DIRECTORY_SEPARATOR."arm64".DIRECTORY_SEPARATOR."mactelnet";
				} else {
					throw new \Exception("Not handled for Distribution: '".$distId."' and architecture: '".$archId."'");
				}
				
			} else {
				throw new \Exception("Not handled for OS Family: '".$osTool->getType()."'");
			}
		}
		return $this->_mtPath;
	}
}