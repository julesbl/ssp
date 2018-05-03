<?php 
namespace w34u\ssp;
error_reporting(E_ALL);

require 'sspadmin/includeheader.php';
$SSP_Config = Configuration::getConfiguration();
$session= new Protect();
$content["title"] = "SSP Demo site";
$content["pathAdmin"] = "sspadmin/";
$tpl_content = [];
if($session->loggedIn){
	$tpl_content['logged_in'] = true;
}
$menu = new MenuGen();
$menu->add($SSP_Config->adminDir, "Administration");
$content["menu"] = $menu->cMenu();
$tpl = new Template($tpl_content, "index.tpl");
$content['content'] = $tpl->output();
$page = new Template($content, "sspgeneraltemplate.tpl");
echo $page->output();
?>