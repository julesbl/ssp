<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple site protection
*   Routine:	testDetectLanguage.php
*   Created:	03/04/2013
*   Descrip:	Detect language from browser
*
*   Revision:	a
*   Rev. Date	03/04/2013
*   Descrip:	Created.
*/
namespace w34u\ssp;
require_once("include.php");
$session = new Protect();

if(!isset($_SESSION['SSP_languageCodeForm'])){
	$_SESSION['SSP_languageCodeForm'] = 'en';
}
$langCode =& $_SESSION['SSP_languageCodeForm'];

$session->lang = $langCode;
$detectedLanguage = Protect::$tranlator->detectBrowserLanguage();
$browserLanguages = Protect::$tranlator->parseBrowserLanguages($_SERVER["HTTP_ACCEPT_LANGUAGE"]);

$templateContent = array(
	'currentLang' => $langCode,
	'detectedLang' => $detectedLanguage,
	'browserLanguages' => print_r($browserLanguages, true),
	'installedLanguages' => print_r(Protect::$tranlator->getLanguages(), true)
);
$template = new Template($templateContent, 'testDetectLanguage.tpl', true);
$template->output();