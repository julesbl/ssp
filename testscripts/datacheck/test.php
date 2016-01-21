<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	SSP - Testing data types
*   Routine:	test.php
*   Created:	29/09/2009
*   Descrip:	Testing the data types routines.
*
*   Revision:	a
*   Rev. Date	29/09/2009
*   Descrip:	Created.
*/
namespace w34u\ssp;
require("include.php");
$session = new Protect();
$dataCheck = new CheckData();

$formLang = new SfcForm(SSP_Path(), "noTable", "languageform");
$formLang->tplf = "testDatatypeLanguage.tpl";
$formLang->formSubmitVar = 'testLanguagechange';
$formLang->fe('select', 'language', 'Language', Protect::$tranlator->getLanguages());
$formLang->fep('deflt = '. $session->lang);
$formLang->setParam('script', 'onChange="this.form.submit()"');
if($formLang->processForm($_POST)){
	$session->lang = $formLang->getField('language');
	session_write_close();
	SSP_Divert(SSP_Path());
}
else{
	$setLanguage = $formLang->create();
}

$form = new SfcForm(SSP_Path(), "noTable", "testDatatype");
$form->tplf = "testDatatype.tpl";
$form->tda('lang', $session->lang);
$form->tda('setLanguage', $setLanguage);
$form->fe("text", "data", "Data to be checked");
$form->fep("dataType = gen");
$dataType = array(
			"text" => "text ". $dataCheck->dataTypes["text"]->validChars,
			"password" => "password ". $dataCheck->dataTypes["password"]->validChars,
			"date" => "date ". $dataCheck->dataTypes["date"]->validChars,
			"time" => "time ". $dataCheck->dataTypes["time"]->validChars,
			"phone" => "phone ". $dataCheck->dataTypes["phone"]->validChars,
			"int" => "int ". $dataCheck->dataTypes["int"]->validChars,
			"real" => "real ". $dataCheck->dataTypes["real"]->validChars,
			"hex" => "hex ". $dataCheck->dataTypes["hex"]->validChars,
			"oct" => "oct ". $dataCheck->dataTypes["oct"]->validChars,
			"bin" => "bin ". $dataCheck->dataTypes["bin"]->validChars,
			"email" => "email ". $dataCheck->dataTypes["email"]->validChars,
			"emailchk" => "emailchk ". $dataCheck->dataTypes["email"]->validChars,
			"dom" => "dom ". $dataCheck->dataTypes["dom"]->validChars,
			"domchk" => "domchk ". $dataCheck->dataTypes["dom"]->validChars,
			"lable" => "lable ". $dataCheck->dataTypes["lable"]->validChars,
			"gen" => "general data, no checking at all!"
		);
$form->fe("select", "dataType", "Data type to check against", $dataType);

if($form->processForm($_POST)){
	if(!$form->error){
		$error = $dataCheck->check($form->getField("dataType"), $form->getField("data"));
		$form->tda("errorNumber", $error);
		$form->tda("errorString", $dataCheck->errorMessage);
		echo $form->create(true);
	}
}
else{
	echo $form->create(true);
}
?>