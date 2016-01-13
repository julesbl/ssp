<?php 
require("sspadmin/includeheader.php");
require($SSP_IncludePath. "htmlobjects.php");
$session= new Protect();
$content["title"] = "SSP Demo site";
$content["pathAdmin"] = "sspadmin/";
$menu = new menuGen();
$menu->add($SSP_Config->adminDir, "Administration");
$content["menu"] = $menu->cMenu();
$page = new Template($content, "sspgeneraltemplate.tpl");
$page->getFile("content", "index.tpl");
echo $page->output();
?>