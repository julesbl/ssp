<?php
namespace w34u\ssp;

require '../includeheader.php';
$cfg = \w34u\ssp\Configuration::getConfiguration();

if($cfg->enableSetup !== true){
	exit('Setup disabled, Enable in configuration, ->enableSetup');
}
$content = [];
if(!isset($_POST['SFC_Submit'])){
	// set up database if not posting the form
	define('RUCKUSING_WORKING_BASE', getcwd());
	$db_config = require RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'ruckusing.conf.php';

	if (isset($db_config['ruckusing_base'])) {
		define('RUCKUSING_BASE', $db_config['ruckusing_base']);
	} else {
		define('RUCKUSING_BASE', dirname(__FILE__));
	}

	require_once RUCKUSING_BASE . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.inc.php';
	$params = [
		'index.php',
		'db:migrate'
	];
	$main = new \Ruckusing_FrameworkRunner($db_config, $params);
	$content['database_creation'] = $main->execute();
}

$session = new Protect();
$ssp = new Setup($session, true);
$admin = new UserAdmin($session, $ssp, '', 'sspsmalltemplate.tpl');
echo $admin->adminCreate($content);

