<?php 
namespace w34u\ssp;
error_reporting(E_ALL);

require 'sspadmin/includeheader.php';
$SSP_Config = Configuration::getConfiguration();
$session= new Protect();
$content["title"] = "SSP Demo site";
$content["pathAdmin"] = "sspadmin/";
$menu = new MenuGen();
$menu->add($SSP_Config->adminDir, "Administration");
$content["menu"] = $menu->cMenu();
$page = new Template($content, "sspgeneraltemplate.tpl");
$page->getFile("content", "index.tpl");
echo $page->output();
?>