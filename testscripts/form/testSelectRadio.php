<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	SSP - w34u
*   Routine:	testSelectRadio.php
*   Created:	02-Mar-2012
*   Descrip:	Test select and radio buttons.
*
*   Revision:	a
*   Rev. Date	02-Mar-2012
*   Descrip:	Created.
*/
namespace w34u\ssp;
require("include.php");

$form = new SfcForm('', 'noTable', 'testform');
$form->tplf = "testSelectRadio.tpl";
// data for selects and radio
$selectBasic = array(
	1 => "Option 1",
	2 => "Option 2",
	3 => "Option 3"
);
$form->fe("select", 'simpleSelect', "Simple Selection", $selectBasic);
$selectComplex = array(
	1 => "option 1",
	2 => array("text" => "option 2", "class" => "option2Class"),
	3 => new SfcSelectOptions('Option 3'),
	4 => new SfcSelectOptions('Optgroup 4', 'optgroup', array(
		41 => "Option 41",
		42 => "Option 42",
		43 => "Option 43",
	)),
	5 => "Option 5",
);
$selectComplex[3]->addAttribute('class', 'option3Class');
$form->fe("select", "selectComplex", "Complex selection", $selectComplex);
//$form->fe("select", 'errorSimple', "Simple Selection", $selectBasic);
//$form->fe("select", "errorComplex", "Complex selection", $selectComplex);
//$form->fe("select", 'noSelect', "No selections", array());
$form->fe("radio", 'radioSelect', "Radio selections", $selectBasic);
$form->fep("deflt=1");
//$form->fe("radio", 'radioSelectError', "Radio error", $selectBasic);
//$form->setParam('validResults', array(1,2,3,4));
if($form->processForm($_POST)){
	if(!$form->error){
		$form->tda("simpleSelectValue", $form->getField("simpleSelect"));
		$form->tda("complexSelectValue", $form->getField("selectComplex"));
		echo $form->create(true);
	}
}
else{
	echo $form->create();
}
?>