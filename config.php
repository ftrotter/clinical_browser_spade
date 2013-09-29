<?php
/**
 * This file imports the config.yaml file into the Globals array. It also serves
 * to provide an example yaml, in comments...
 * @author Fred Trotter <fred.trotter@gmail.com>
 * @package Spade
 * @copyright Copyright (c) 2013 Fred Trotter and DocGraph 
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License v3 or later
 */

	require_once('util/spyc/spyc.php');
	require_once('util/functions.php');

	if(file_exists('../config.yaml')){
		$config = '../config.yaml';
	}else{
		$config = dirname(__FILE__).'/config.yaml';
	}

	$config = Spyc::YAMLLoad($config);
	//must use the ../ since this file is loaded from the public directory


	//make all config values global..
	$config_sub_array = array();
	foreach($config as $config_key => $config_value){
		$GLOBALS[$config_key] = $config_value;
		$config_sub_array[$config_key] = $config_value;
	}

	$base_url = base_url();
	$config_sub_array['base_url'] = $base_url;
	//add a subarray just for smarty
	$GLOBALS['config'] = $config_sub_array;

	$GLOBALS['db_link'] = mysql_connect(
				$config['mysql_host'], 
				$config['mysql_user'], 
				$config['mysql_password']) 
		or die(mysql_error());
	mysql_select_db($GLOBALS['mysql_database'], $GLOBALS['db_link']) 
		or die(mysql_error());
	//if we need to login using Google IDs

	date_default_timezone_set($config['timezone']);

/*/ The contents of the config.yaml file should look something like this...
---
base_url: https://yoursite.com
debug: false
timezone: America/New_York
mysql_user: root
mysql_password: password
mysql_host: localhost
mysql_database: record
*/	

?>
