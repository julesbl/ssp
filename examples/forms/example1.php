<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	example1.php
*   Created:	23/03/2009
*   Descrip:	Basic for usage.
*
*   Revision:	a
*   Rev. Date	23/03/2009
*   Descrip:	Created.
*/
namespace w34u\ssp;
// include libraries
require("../../sspadmin/includeheader.php");

$mainTemplate = new Template("Main Template", "exampleMain.tpl");

// form definition
// id name for the table is used for detection of submission and error messages
$form = new sfc\Form(SSP_Path(true), "tableNameForSql", "idNameforTable");
$form->tpl = $mainTemplate; // main template to enclose the form, not required, form inserted into {content}
$form->tplf = "example1.tpl"; // display template for the form
$form->tda("miscTplData", "Some data for display on the form template, password is 'thingy'");
$form->fe("text", "firstElement", "First element, a text box");
$form->fep("required=true, dataType=text"); // element is required and is of data type text
$form->fe("password", "pasword", "enter a password");
$form->fep("required=true, dataType=password, load=false");
$form->fe('radio', 'radio_test', 'Radio test button separate labels', [0 => 'First option', 1 => 'Second option']);
$form->fe('radio', 'radio_test_2', 'Radio test button encapsulated labels', [0 => 'First option 2', 1 => 'Second option 2']);
$form->currentElelementObject->encap = true;
$form->fe('radio', 'radio_test_3', 'Radio test button separate labels, labels after', [0 => 'First option 3', 1 => 'Second option 3']);
$form->currentElelementObject->textBefore = false;
$form->addHidden("hiddenStuff", "Some hidden stuff", "text"); // hidden field

// check for submission
if($form->processForm($_POST)){
	// check for error
	if(!$form->error){
		// check password
		if($form->getField("pasword") != "thingy"){
			$form->addError("Error in the form");
			$form->setError("pasword", "Error in password");
			echo $form->create(true);
		}
		else{
			echo "Submission succesful";
		}
	}
}
else{
	echo $form->create();
}
?>