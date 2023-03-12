# MTM-MacTelnet

### What is this?

Make Mac Telnet connections to other hosts and issue commands to the shell.
You can also connect to one device and use that to connect to another.
e.g. mac telnet to a routeros device, then use that shell to connect to a routerOS device or use the ssh lib to bounce to a linux host.
Then use the second device to connect to a third device (linux or routeros) and so on

Currently it is not possible to mac telnet from linux to routeros because the authentication algo change around 6.43.9 and has not yet been reverse enginnered.

You can execute any command you want. There is full parity with the underlying shells

## Install:

```
composer require merlinthemagic/mtm-mactelnet

```

## Post install


### ARM64 based Ubuntu (e.g. Raspberry PI 4B):
```

chown php-user:php-user /path/to/MTM/MacTelnet/Resources/MacTelnet/Ubuntu/arm64/mactelnet
chmod +x /path/to/MTM/MacTelnet/Resources/MacTelnet/Ubuntu/arm64/mactelnet

```
#### get all devices announcing themselves via MNDP

```

$toolObj	= \MTM\MacTelnet\Facts::getTools()->getMacTelnet();
$msTimeout	= 2500;
$devObjs	= $toolObj->discover(msTimeout);
foreach ($devObjs as $devObj) {
	
	//get data available via MNDP announcements
	echo $devObj->getLicenseId()."\n";
	echo $devObj->getModel()."\n";
	
	//execute commands:
	$devObj->setUsername("admin")->setPassword("");
	
	$strCmd		= ":put [/system/routerboard/settings/get boot-device];";
	echo $devObj->getCmdReturn($strCmd)."\n";
	
}

```

#### Get an SSH shell on a routeros device using password (You will need the MTM-SSH lib for this)
```
$ipAddress		= "192.168.88.1";
$username		= \MTM\Shells\Factories::getTools()->getRouterOs()->formatUsername("username");
$password		= "verySecretSshPassword";

$sshCtrlObj		= \MTM\SSH\Factories::getShells()->passwordAuthentication($ipAddress, $username, $password	);
```

#### Bounce to another routerOS device using mac telnet:
```
$macAddress		= "112233445566";
$username		= \MTM\Shells\Factories::getTools()->getRouterOs()->formatUsername("username");
$password		= "verySecret";

$ctrlObj		= \MTM\MacTelnet\Facts::getShells()->passwordAuthentication($macAddress, $username, $password, $sshCtrlObj);

```

#### Get a shell on RouterOS using MacTelnet (Requires MacTelnet: https://github.com/haakonnessjoen/MAC-Telnet):
```
$macAddress		= "112233445566";
$username		= \MTM\Shells\Factories::getTools()->getRouterOs()->formatUsername("username");
$password		= "verySecret";

$ctrlObj		= \MTM\MacTelnet\Facts::getShells()->passwordAuthentication($macAddress, $username, $password);

```


#### Start running commands: RouterOS
```
$data		= $ctrlObj->getCmd("/system resource print")->exec()->get();
echo $data; //list of system resources

$data		= $ctrlObj->getCmd("/interface print")->exec()->get();
echo $data; //list of interfaces
```