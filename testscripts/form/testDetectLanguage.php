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
require_once("include.php");
$session = new SSP_Protect();
require_once($SSP_IncludePath. 'SSP_translate.php');
require_once($SSP_TranslatePath. 'lang_fr.conf.php');
require_once($SSP_TranslatePath. 'lang_fr.php');
require_once($SSP_TranslatePath. 'lang_en.conf.php');
require_once($SSP_TranslatePath. 'lang_en.php');

if(!isset($_SESSION['SSP_languageCodeForm'])){
	$_SESSION['SSP_languageCodeForm'] = 'en';
}
$langCode =& $_SESSION['SSP_languageCodeForm'];

$lang = new SSP_translate($langCode, $SSP_TranslatePath);
$detectedLanguage = $lang->detectBrowserLanguage();
$browserLanguages = $lang->parseBrowserLanguages($_SERVER["HTTP_ACCEPT_LANGUAGE"]);

$templateContent = array(
	'currentLang' => $langCode,
	'detectedLang' => $detectedLanguage,
	'browserLanguages' => print_r($browserLanguages, true),
	'installedLanguages' => print_r($lang->getLanguages(), true)
);
$template = new SSP_Template($templateContent, 'testDetectLanguage.tpl', true);
$template->output();