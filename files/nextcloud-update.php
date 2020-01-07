#!/usr/bin/php-cli

//<?php
/////////////////////////////////////////////////////////
// This script updates nextcloud trusted_domains setting
// adds there onion hostname which was given as cli pamameter
// It also creates lighttpd config which allows access
// from that onion hostname only to nextcloud
//

function update_nextcloud_config($nextcloud_conf, $onion) {
	if (file_exists($nextcloud_conf)) {
		include $nextcloud_conf;

		//add onion to trust domain
		$bb=array_search($onion,$CONFIG["trusted_domains"]);
		if(!$bb){
			array_push($CONFIG['trusted_domains'],$onion);
		}else{
			return false;
		}
		//write updated config from stdout
		fclose(STDOUT);
		$STDOUT = fopen($nextcloud_conf, 'wb');

		printf("<?php\n");
		printf('$CONFIG = ');
		$tmp=var_export($CONFIG);
		printf(";\n");

		fclose($STDOUT);
	} else {
		return false;
	}
	return true;
}

function update_lighttpd_config($lighttpd_conf,$onion) {
	if (file_exists($lighttpd_conf)) {
		$tmp_conf = file_get_contents($lighttpd_conf);
		if (strstr($tmp_conf, $onion)) {
			//in case that config contains our
			//onion address end here
			return false;
		}
	}
	$fp = fopen($lighttpd_conf, 'w');
	fprintf($fp,'$HTTP["host"] ==  "%s" {'."\n", $onion);
	fprintf($fp,'	$HTTP["url"] !~ "^/nextcloud" {'."\n");
	fprintf($fp,'		url.access-deny = ( "" )'."\n");
	fprintf($fp,"	}\n}\n");
	fclose($fp);
	return true;
}

function reload_lighttpd() {
	$output = shell_exec('/etc/init.d/lighttpd restart');
	echo "$output";
}

function print_help(){
	echo "Help:\n";
}

$nextcloud_conf='/srv/www/nextcloud/config/config.php';
$lighttpd_conf="/etc/lighttpd/conf.d/nextcloud-hs.conf";

if(count($argv)>=3){
	if($argv[1]=="--update-onion"){
		if(strlen($argv[2])>22){
			$onion=$argv[2];
			$ret_lighttpd=update_lighttpd_config($lighttpd_conf, $onion);
			$ret_nextcloud=update_nextcloud_config($nextcloud_conf, $onion);

			if($ret_lighttpd == true OR $ret_nextcloud == true){
				//in case of config change reload lighttpd
				reload_lighttpd();
			}
		}else{
			echo "Error! Wrong lenght of onion address! Make sure you are using HSv3\n";
		}
	}elseif($argv[1] == "--help"){
		print_help();
	}else{
		echo "Error! Wrong command!\n";
	}
}else{
	print_help();
}

