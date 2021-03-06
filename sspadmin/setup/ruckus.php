#!/usr/bin/env php
<?php
if(php_sapi_name() !== "cli"){
	exit();
}
$path_to_vendor_directory = __DIR__. '/../../vendor/';
$files = array(
		__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
		__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
		__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
		__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
		__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'autoload.php',
);
$loader = null;
foreach ($files as $file) {
	if (file_exists($file)) {
		$loader = require_once $file;
		break;
	}
}
if($loader === null){
	die('Failed to load composer!!!!!');
}
if(file_exists(__DIR__. '/../devinclude.php')){
	require __DIR__. '/../devinclude.php';
}
$ssp_config = \w34u\ssp\Configuration::getConfiguration();
if($ssp_config->enableSetup !== true){
	exit('Setup disabled, Enable in configuration, ->enableSetup');
}

if(file_exists(__DIR__. '/../devinclude.php')){
	require __DIR__. '/../devinclude.php';
}

define('RUCKUSING_WORKING_BASE', getcwd());
$db_config = require RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'ruckusing.conf.php';

if (isset($db_config['ruckusing_base'])) {
    define('RUCKUSING_BASE', $db_config['ruckusing_base']);
} else {
    define('RUCKUSING_BASE', dirname(__FILE__));
}

require_once RUCKUSING_BASE . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.inc.php';

$main = new Ruckusing_FrameworkRunner($db_config, $argv);
echo $main->execute();
