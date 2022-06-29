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

#### Get an SSH shell on a routeros device using password (You will need the MTM-SSH lib for this)
```
$username		= \MTM\SSH\Factories::getShells()->getRouterOsTool()->getFormattedUsername("username");
$ctrlObj		= \MTM\SSH\Factories::getShells()->passwordAuthentication("IpAddress", $username, "password");
```

#### Bounce to another routerOS device using mac telnet:
```
$username2	= \MTM\SSH\Factories::getShells()->getRouterOsTool()->getFormattedUsername("username2");
$ctrlObj		= \MTM\MacTelnet\Factories::getShells()->passwordAuthentication("mac-address", $username2, "password", $ctrlObj);

```

#### Start running commands: routeros
```
$data		= $ctrlObj->getCmd("/system resource print")->exec()->get();
echo $data; //list of system resources

$data		= $ctrlObj->getCmd("/interface print")->exec()->get();
echo $data; //list of interfaces
```