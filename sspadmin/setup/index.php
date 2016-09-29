<?php
require '../includeheader.php';

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
$main = new Ruckusing_FrameworkRunner($db_config, $params);
echo $main->execute();