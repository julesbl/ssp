<?php
require_once("include.php");
$session = new Protect();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Form Test2</title>
</head>
<body>
<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	    Template routines test
*   Routine:	test1upload.php
*   Created:	05/04/2007
*   Descrip:	testing for file upload capbility.
*
*   Revision:	a
*   Rev. Date	05/04/2007
*   Descrip:	Created.
*/
class testForm{
	// simple test form class

	function createForm($formData){
		$form = new Template($formData, "test1upload.tpl", false);
		$form->encode=false;
		$form->output();
		return($form->output);
	}
	
	function previewForm($formData){
		$form = new Template($formData, "test1uploadpreview.tpl", false);
		$form->encode=false;
		$form->output();
		return($form->output);
	}
	
	function modFile($fileName){
		return($fileName. "10");
	}
}

$form = new SfcForm("test1upload.php", "TestSave");
$form->templateRoutine = 'testForm::createForm';
$form->setPreview("testform::previewForm");
$form->buildForm = false;
$form->fe("textarea", "textarea", "Testing text area box", "text area");
$form->fep("width=50,lines=10");
$validTypes = array(".jpg", ".gif", ".png");

$form->fe("file", "image1", "upload an image");
$form->fileObjects["image1"] = new SFC_file($form->elements["image1"], "images/", "images/", $validTypes, "102400", "testForm::modFile");
$form->fileObjects["image1"]->setPreview("preview/", "preview/");

$form->fe("file", "image2", "upload another image");
$form->fileObjects["image2"] = new SFC_file($form->elements["image2"], "images/", "images/", $validTypes, "102400", "testForm::modFile");
$form->fileObjects["image2"]->setPreview("preview/", "preview/");
$form->fe("submit", "submit1", "Submit Now");

$form->funcTokenMake = "SSP_FormToken";
$form->funcTokenCheck = "SSP_FormTokenCheck";

if($form->processForm($_POST)){
    if(!$form->error and $form->previewSave){
		echo $form->create(true);
    }
}
else{
    echo $form->create();
}
echo "<pre>";
// var_dump($form->hiddenFields);
echo "</pre>";
?>
</body>
</html>