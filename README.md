# Tor Hidden service configurator
**tor-hs** packages tries to simplify creating of hidden services on OpenWrt routers.

## Requirements
To run **tor-hs**, you need Tor package with uci config support (it was added
with [this commit](https://github.com/openwrt/packages/commit/ca6528f002d74445e3d0a336aeb9074fc337307a) ).

## Instalation
To install package simple run
```
opkg update
opkg install tor-hs
```

## Configuration
Uci configuration is located in **/etc/config/tor-hs**

### Required section of configuration
config tor-hs common
| Type | Name | Default | Description |
| ------ | ------ | ------ | ------ |
| option |GenConf | /etc/tor/torrc_generated|Generated config by tor-hs.|
| option | HSDir |/etc/tor/hidden_service|Directory with meta-data for hidden services (hostname,keys,etc).|
| option | RestartTor | true| It will restart tor after running **/etc/init.d/tor-hs start**.|
| option | UpdateTorConf | true|Update /etc/config/tor with config from **GenConf** option.|

### Hidden service configuration
config hidden-service

| Type | Name | Example value | Description |
| ------ | ------ | ------ | ------ |
|	option | Name | sshd| Name of hidden service. It is used as directory name in **HSDir**|
|	option | Description| Hidden service for ssh| Description used in **rpcd** service|
|	option | Enabled |false| Enable hidden service after running **tor-hs** init script|
|	option |IPv4 |127.0.0.1|Local IPv4 address of service. Service could run on another device, in that case OpenWrt will redirect comunication.  |
|	list | PublicLocalPort| 2222;22| Public port is port accesible via Tor network. Local port is normal port of service.|
|option| HookScript |'/etc/tor/nextcloud-update.php'| Path to script which is executed after starting tor-hs. Script is executed with paramters **--update-onion** **hostname** . Hostname is replaced with Onion v3 address for given hidden service. |

### RPCD
```
root@turris:/# ubus call tor_rpcd.sh list-hs '{}'
{
	"hs-list": [
		{
			"name": "sshd",
			"description": "Hidden service for ssh",
			"enabled": "1",
			"ipv4": "127.0.0.1",
			"hostname": "****hidden-service-hostname****.onion",
			"ports": [
				"22;22"
			]
		}
	]
}
```

