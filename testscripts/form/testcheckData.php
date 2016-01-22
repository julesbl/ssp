<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	    Template routines test
*   Routine:	testcheckData.php
*   Created:	29/09/2009
*   Descrip:	Data checking of form entries.
*
*   Revision:	a
*   Rev. Date	29/09/2009
*   Descrip:	Created.
*/
namespace w34u\ssp;
require_once("include.php");
$session = new Protect();

// add french translations for this page
Translate::addToLanguage('fr', array(
	'Text input' => 'La saisie de texte',
	'Password input' => 'Mot de passe d\'entrée',
	'Url input' => 'Url entrée',
	'Email input' => 'entrée E-mail',
	'Email no dot or at' => 'Envoyer aucun point ou à',
	'Email too long' => 'Envoyer à trop long',
	'Date input' => 'Date de l\'entrée',
	'Time input' => 'saisie de l\'heure',
	'Phone input' => 'téléphone d\'entrée',
	'Integer input' => 'entrée entier',
	'Real input' => 'd\'entrée réel',
	'Hexadecimal input' => 'une entrée hexadécimale',
	'Octal input' => 'octal entrée',
	'Binary input' => 'entrée binaire',
	'Text input required' => 'Saisie de texte nécessaire',
	'Text minimum number of characters required' => 'Nombre minimum de caractères requis du texte',
	'Text maximum number of characters required' => 'Nombre maximum de caractères requis du texte',
	'A global error' => 'Une erreur globale',
	'A local error added to the password field' => 'Une erreur locale ajoutés au champ Mot de passe',
));

// local/global error list
if(!isset($_SESSION['SSP_errorLocal'])){
	$_SESSION['SSP_errorLocal'] = true;
}
$errorLocal =& $_SESSION['SSP_errorLocal'];

$formLang = new SfcForm(SSP_Path(), "noTable", "languageform");
$formLang->tplf = "testDatatypeLanguage.tpl";
$formLang->formSubmitVar = 'testLanguagechange';
$formLang->fe('select', 'language', 'Language', Protect::$tranlator->getLanguages());
$formLang->fep('deflt = '. $session->lang);
$formLang->fe('check', 'localError', 'Errors local to fields', array(0,1));
$formLang->fep('deflt = '. $errorLocal);
if($formLang->processForm($_POST)){
	$session->lang = $formLang->getField('language');
	if($formLang->getField('localError') == 1){
		$errorLocal = true;
	}
	else{
		$errorLocal = false;
	}
	session_write_close();
	SSP_Divert(SSP_Path());
}
else{
	$setLanguage = $formLang->create();
}


$form = new SfcForm("testcheckData.php", "TestSaveTable", "testdataform");
$form->tplf = "testCheckData_". $session->lang. ".tpl";
$form->tda('lang', $session->lang);
$form->tda('setLanguage', $setLanguage);
if($errorLocal){
	$form->tda('localErrors', 1);
}
$form->errorsLocal = $errorLocal;
$form->errorAutoFormDisplay = false;

$form->fe("text", "textType", "Text input", ">");

$form->fe("text", "passwordType", "Password input", ">");
$form->fep("dataType=password");

$form->fe("text", "domType", "Url input", ">");
$form->fep("dataType=dom");

$form->fe("text", "emailType", "Email input", ">");
$form->fep("dataType=email");
$form->fe("text", "emailDotAt", "Email no dot or at", "test");
$form->fep("dataType=email");
$form->fe("text", "emailLength", "Email too long", "abcdefghijlklmonpoqrstuvwxyzabcdefghijlklmonpoqrstuvwxyzabcdefghijlklmonpoqrstuvwxyz@abcdefghijlklmonpoqrstabcdefghijbflklmon.com");
$form->fep("dataType=email");

$form->fe("text", "dateType", "Date input", ">");
$form->fep("dataType=date");

$form->fe("text", "timeType", "Time input", ">");
$form->fep("dataType=time");

$form->fe("text", "phoneType", "Phone input", ">");
$form->fep("dataType=phone");

$form->fe("text", "intType", "Integer input", ">");
$form->fep("dataType=int");

$form->fe("text", "realType", "Real input", ">");
$form->fep("dataType=real");

$form->fe("text", "hexType", "Hexadecimal input", ">");
$form->fep("dataType=hex");

$form->fe("text", "octType", "Octal input", ">");
$form->fep("dataType=oct");

$form->fe("text", "binType", "Binary input", ">");
$form->fep("dataType=bin");

$form->fe("text", "textRequired", "Text input required");
$form->fep('required = true');
$form->fe("text", "textMinChars", "Text minimum number of characters required", '12');
$form->fep('minChar = 3');
$form->fe("text", "textMaxChars", "Text maximum number of characters required", '123456');
$form->fep('maxChar = 5');


if($form->processForm($_POST)){
    if(!$form->error){
		echo $form->create(true);
    }
	else{
		$form->addError('A global error');
		$form->setError('passwordType', 'A local error added to the password field');
		echo $form->create(true);
	}
}
else{
    echo $form->create();
}
