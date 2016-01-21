<?php
namespace w34u\ssp;
require_once("include.php");
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
*   Routine:	test1.php
*   Created:	12/05/2006
*   Descrip:	Testing basic form fuctionality, all main field types with sql generation.
*
*   Revision:	a
*   Rev. Date	08/08/2006
*   Descrip:	Created.
*/
class testForm{
	// simple test form class
	static function test($form){
		if(strlen($form->getField("testtext"))<3){
			$form->addError("Checking routine does not like testtexts length");
		}
	}

	static function previewForm($formData){
		$form = new Template($formData, "test2preview.tpl", false);
		$form->encode=false;
		$form->output();
		return($form->output);
	}
}

$form = new SfcForm("test2.php", "TestSaveTable", "test2form");
$form->formCheck = '\w34u\ssp\testForm::test';
$form->tplf = "test2.tpl";
$form->setPreview("\w34u\ssp\testform::previewForm");

$form->fe("text", "testtext", "Testing input box", "test input");
$params = "dataType = text, elClass = boxClass, maxLength = 100, accessKey=a,tabIndex=10,style=border-width:10px, ldir=ltr,lang=en,width=100,required=true, minChar=3";
$form->fep($params);
$form->fe("password", "textpass", "Testing password box");
$form->fep("dataType = password,required=true,encap=true,textBefore=false");
$form->fe("textarea", "textarea", "Testing text area box", "text area");
$form->fep("width=50,lines=10");
$selectData = array("first"=>"First Select","second"=>"Second one","Mouse"=>"It's a mouse");
$form->fe("select", "testSelect", "Testing dropdown", $selectData);
$form->fep("deflt=second");
$form->fe("select", "testMulti", "Testing Multi", $selectData);
$form->fep("lines=4");
$checkData = array("unchecked", "checked");
$form->fe("check", "testCheck", "Testing Check", $checkData);
$radioData = array(1=>"First One", 2=>"Second entry", 3=>"third");
$form->fe("radio", "testRadio", "Testing radio buttons", $radioData);
// $form->fep("required=true");
$form->fe("startrow", "1");
$form->fe("colrow", "2");
$form->fe("submit", "submit1", "Submit Now");
$form->fe("endrow", "3");

if($form->processForm($_POST)){
    if(!$form->error and $form->previewSave){
		echo $form->create(true);
        echo "<p>Form submitted</p>";
        echo "<pre>";
        var_dump($_POST);
        echo "</pre>";
        $form->whereCondition = "id = ?";
        $form->whereValues = array("asdlkjflka");
        $result1 = $form->querySelect();
        echo "Select query for form";
        echo "<pre>";
        var_dump($result1);
        echo "</pre>";
        $form->alsoAdd = array("id"=>"asdlkjflka");
        $result2 = $form->querySave();
        echo "Insert query for form";
        echo "<pre>";
        var_dump($result2);
        echo "</pre>";
        $result3 = $form->querySave(true);
        echo "Update query for form";
        echo "<pre>";
        var_dump($result3);
        echo "</pre>";
    }
}
else{
    echo $form->create();
}
echo "<pre>";
// var_dump($form);
echo "</pre>";
?>
</body>
</html>