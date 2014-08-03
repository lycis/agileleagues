<?php
if (!defined('RDS_HOSTNAME')) {
	define('RDS_HOSTNAME', $_SERVER['RDS_HOSTNAME']);
 	define('RDS_USERNAME', $_SERVER['RDS_USERNAME']);
 	define('RDS_PASSWORD', $_SERVER['RDS_PASSWORD']);
 	define('RDS_DB_NAME', $_SERVER['RDS_DB_NAME']);
}
die(var_dump(RDS_DB_NAME));
					
class DATABASE_CONFIG {
					
	public $default = array(
	    'datasource' => 'Database/Mysql',
	    'persistent' => false,
	    'host' => RDS_HOSTNAME,
	    'login' => RDS_USERNAME,
	    'password' => RDS_PASSWORD,
	    'database' => 'agileleagues',
	    'prefix' => '',
	    'encoding' => 'utf8',
  	);
}

