<?php
//� 2023 Martin Peter Madsen
namespace MTM\MacTelnet\Tools\MacTelnet;

abstract class Discover extends Alpha
{
	public function discover($timeoutMs=10000)
	{
		$this->isUsign32Int($timeoutMs, true);
		$tTime		= intval($timeoutMs / 1000);
		
		$devObjs	= array();
		
		//lots of hoops to suppress script echo: "Searching for MikroTik routers... Abort with CTRL+C."
		$strCmd		= "MTM=\$(".$this->getMacTelnetPath()." -l -B -t 5 2>&1 | base64 -w0); echo \$MTM | base64 -d;";
		exec($strCmd, $rData, $status);
		$lines		= array_values(array_filter(array_map("trim", $rData)));
		unset($lines[0]);
		unset($lines[1]);
		foreach ($lines as $line) {
			$parts		= array_values(array_filter(array_map("trim", explode("','", $line))));
			foreach ($parts as $pId => $part) {
				$parts[$pId]	= trim($part, "'");
			}
			$type	= null;
			if (array_key_exists(2, $parts) === true) {
				if (strtolower($parts[2]) === "mikrotik") {
					$type	= "mikrotik";
				}
			}
			if ($type === "mikrotik") {
				if (array_key_exists(7, $parts) === true) {
					$licId		= $parts[6];
					if (array_key_exists($licId, $devObjs) === false) {
						$devObj		= \MTM\MacTelnet\Facts::getDevices()->getMikrotik();
						$devObj->setIdentity($parts[1])->setLicenseId($parts[6])->setModel($parts[4]);
						$devObj->setUptime(intval($parts[5]))->setCurrentVersion($parts[3]);
						$devObjs[$licId]		= $devObj;
					} else {
						$devObj					= $devObjs[$licId];
					}
					
					$macAddr	= "";
					foreach (explode(":", $parts[0]) as $part) {
						$macAddr	.= str_repeat("0", (2 - strlen($part))).$part;
					}
					$devObj->addInterface($parts[7], $macAddr);

				} else {
					throw new \Exception("Mikrotik device did not return enough detail");
				}

			} else {
				throw new \Exception("Not handled");
			}
		}

		return array_values($devObjs);
	}
}