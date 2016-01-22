<?php
/*
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:    Simple Form Creation
*   Routine:	SfcForm.php
*   Created:	07/01/2005
*   Descrip:	Classes for creating and handling database forms.
*
*   Copyright 2005-2016 Julian Blundell, w34u
*
*   This file is part of Simple Site Protection (SSP).
*
*   SSP is free software; you can redistribute it and/or modify
*   it under the terms of the The MIT License (MIT)
*   as published by the Open Source Initiative.
*
*   SSP is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   The MIT License (MIT) for more details.
*
*   Revision:	a
*   Rev. Date	07/01/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	18/07/2006
*   Descrip:	Updated to produce SQL injection resistent queries.
*
*   Revision:	c
*   Rev. Date	18/08/2006
*   Descrip:	Simplified form creation, data type checking, hardened against JavaScript injection attacks.
*   
*   Revision:	d
*   Rev. Date	24/09/2009
*   Descrip:	Extensive itterative improvements, new class for input error handling
*
*   Revision:	e
*   Rev. Date	23/02/2011
*   Descrip:	Changed to php5 class system.
*
*   Revision:	f
*   Rev. Date	3/05/2013
*   Descrip:	Added internationalisation facilities.
*
*   Revision:	g
*   Rev. Date	13/01/2016
*   Descrip:	Changed to psr-4.
*/

namespace w34u\ssp;

/**
 * Class to define the form elements
 */
class SfcFe{

    // html properties
	/** type of form element - text, password, select, textarea, startrow, endrow, colrow, submit, reset, button, image, file, check, radio
	 * @var string */
    public $type = "text";
	/** @var string - name of form field, also its id and database field */
    public $name = "";
	/** @var string id for form element if different from the element name */
	public $id = "";
	/** @var string - description for field, used in form and display, is also text for submit, reset and button. */
    public $description = "";
	/** Placeholder text
	 * @var string  */
	public $placeholder = "";
	/** @var string addtional class to be applid to the element */
    public $elClass = "";
	/** @var string addtional class to be applied to the label */
    public $lClass = "";
	/** @var string default type for the <button tag */
    public $buttonType = 'submit';
	/** @var int maximum amount of characters that can be entered, html limiter */
    public $maxLength = "";
	/** @var array data for dropdowns, checkboxes etc. */
    public $data = "";
	/** @var array valid results for a selection or radio button, particularly useful for ajax loading of the element */
	public $validResults = array();
	/** @var string default value for elements. */
    public $deflt = "";
    public $accessKey = ""; // give the element and access key
    public $image = ""; // relative path for image
    public $orientation = "horizontal"; // radio, vertical or horizontal
    public $radioDesc = true; // show the radio buttons text;
	public $textBefore = true; // text before radio buttons and encapsulated elements
	public $encap = false; // put the form element in the description label
    public $script = ""; // event and javascript
    public $useExternal = false; // use external data in elementSelections for dropdown etc.
    public $tabIndex = ""; // tabindex for field
    public $readonly = false; // if set to true the text or text area field is readonly, user cannot modify field
    public $disabled = false; // if set to true text or textarea is disabled on first display
	public $helpText = ""; // help text for filling in the form used in title and used in third column if enabled
	public $genText = ""; // general text to be put in box with input element
    public $style = ""; // locally applied style
    public $ldir = ""; // language direction, can be ltr or rtl
    public $lang = ""; // language code for element
    public $width =""; // width in characters for input field
    public $lines = ""; // number of lines in textarea, dropdown, select
	/** @var string accept field for the file field */
	public $accept = "";

    // sql properties
    public $dbField = ""; // database field name if different from element name.
    public $load = true; // If false the field is never loaded from input data
    public $sql = true; // If false sql is never generated for this field

	// error handling properties
    /*
    dataType, can be:
        text - 0 to 9, a - z, A - Z, \n \r \t . ' " + - _ space
        password - 0 to 9, a - z, A - Z
        dom - <= 128 char, 0-9, a-z, A-Z, ._,/ at least one dot
        domchk - checks the domain
        email - <= 128 char, @, 0-9, a-z, A-Z, _.+-
        emailchk - also checks the domain
		date - 0 to 9, /
		time - 0 to 9, :
        phone - 0-9() +-. space
        int - 0-9 -
        real - 0-9 . - e
        hex - 0-9, a-f, A-F
        oct - 0-7
        bin - 0,1
        gen - any character - when re-displayed any special characters are converted to html special entities and then converted back to characters on submission
    */
    public $dataType = "text"; // data type
    public $field = ""; // data for this element returned from form submission or sql load
    public $required = false; // field is required
    public $maxChar = '0'; // maximum number characters allowed, throws error if greater, 0 means no test unless in the data type.
	public $minChar = '0'; // minimum number of chars, less than throws error
	public $precision = false; // if given a number will round a real number to this
	public $error = false; // Can be set by $this->check() or external code
	/** @var array Error strings to be displayed locally */
	public $errorText = array();
	public $check = false; // check value entered
	public $errorFunction = ''; // used by call_user_func, function($elements, $more) elements and additional data passed by reference, do not put brackets on function name

    /**
	 * Constructo for a form element
	 * @global int $SFC_Page page variable, not used at the moment
	 * @param string $type - type of element eg. text, select
	 * @param string $name - name of the element, used for id as well
	 * @param string $description - description used in label and in alt unless help is specified
	 * @param array $data - data for selects, radio etc.
	 */
	function __construct($type, $name, $description="", $data="") {
		global $SFC_Page;
        /*
        Constructor for the class
        Simply initialises the element properties to defualt if called with no parameters, will initialise any properties specified.
        */
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
		if(is_array($data)){
			$this->data = $data;
		}
		elseif($data != ""){
			$this->deflt = $data;
		}
		if($SFC_Page=""){
			// if SFC_Page has not been assigned, set it to zero
			$this->page = 0;
		}
		else{
			$this->page = $SFC_Page;
		}
        // get properties
    }

	/**
	 * Basic element data checking
	 * @param array $errorStrings - array of strings to return error
	 * @return bool - true on error
	 */
	function checkField($errorStrings){
		if($this->required and trim($this->field) == ""){
			// checks for an empty required field
			$this->addError(sprintf($errorStrings["required"], $this->description));
		}
		if($this->maxChar > 0 and (strlen($this->field) > $this->maxChar)){
			$this->addError(sprintf($errorStrings["maxChar"], $this->description, $this->maxChar));
		}
		if($this->minChar > 0 and (strlen($this->field) < $this->minChar)){
			$this->addError(sprintf($errorStrings["minChar"], $this->description, $this->minChar));
		}
		if($this->check and call_user_func($this->errorFunction, $this->field)){
			$this->addError($this->errorFunctionText);
		}
		if($this->type=="radio" and trim($this->field) == ""){
			// checks for an empty radio button field
			$this->addError(sprintf($errorStrings["required"], $this->description));
		}
		if($this->dataType=="real" and $this->precision){
			// rounds field to correct precision
			$this->field = round($this->field, $this->precision);
		}
		if($this->type == 'select' or $this->type == 'radio' and trim($this->field) !== ""){
			// get valid results if not already supplied
			if(count($this->validResults == 0)){
				$this->getValidResults($this->data);
			}
			if(array_search(trim($this->field), $this->validResults) === false){
				$this->addError(sprintf($errorStrings["errorVal"], $this->name));
			}
		}
		return($this->error);
	}
	
	/**
	 * Gets the valid results from the field data
	 * @param array $data - field data
	 */
	private function getValidResults($data){
		foreach($data as $key => $options){
			if(is_object($options) and isset($options->type) and $options->type == "optgroup"){
				$this->getValidResults($options->options);
			}
			else{
				$this->validResults[] = $key;
			}
		}
	}

    /**
	 * Add and error ro the field
	 * @param string $errorText - error to be added to the field
	 */
	public function addError($errorText){
        // adds an error to the list of errors, usually triggers the error flag
        $this->errorText[] = $errorText;
        $this->error = true;
    }
}

/**
 * Option object used for select options 
 */
class SfcSelectOptions{
	/** @var string text description for option or optgroup */
	public $text = "";
	/** @var string type of option, can be optgroup */
	public $type = "option";
	/** @var array optons for an optgroup */
	public $options = array();
	
	/**
	 * Constructor
	 * @param string $text - text for option or optgroup
	 * @param string $type - type of option, can be option or optgroup
	 * @param array $options - options for optgroup
	 */
	public function __construct($text, $type="option", $options=array()) {
		$this->text = $text;
		$this->type = $type;
		$this->options = $options;
	}
	
	/**
	 * Add an attribute to the html
	 * @param string $attributeName - attributes name
	 * @param string $value - attribute value
	 */
	public function addAttribute($attributeName, $value){
		$this->$attributeName = $value;
		return($this);
	}
}

class SfcHidden{
    // Element for hidden fields added to the form as the SfcForm::hiddenFields array
    public $name; // name of field
    public $data; // data to be sored in hidden field
    public $dataType = "text"; // type of data to be stored, see SFC_FE for more information
    public $elClass = ""; // class assigned to the hidden element

    function __construct($name, $data, $dataType = "", $elClass=""){
        // constructor
        $this->name = $name;
        $this->data = $data;
        $this->elClass = $elClass;
        if($dataType!=""){
        	$this->dataType = $dataType;
        }
    }
}

    /**
    Class to create the form together with queries to populate the form and update the database.

    The resultant query is designed to be processed by prepare and execute type statements to prevent SQL injection attacks, if the data from the form has been automatically slashed, ie. get_magic_quotes_gpc() returns true, the slashes will be stripped using stripslashes().
    */

class SfcForm{

    // data for form creation and processing
    public $elements = array(); // array of form elements of SfcFe defining the form.
    public $currentElement = ""; // current element name
    public $errorElement = "Duplicate element name %s in this form for data table %s";
    public $elementSelections = array(); // array of arrays with data for element dropdowns etc.
    public $data = array(); // data from query, post or get array to populate the form
    public $fileObjects = array(); // file objects for file uploads
    public $method = "post"; // get or post
    public $action; // path to script to be actioned by form.
    public $enctype = ""; // eg. multipart form-data
    public $script = ""; // javascript for form
    public $formT; // target name of window or frame
    public $hiddenFields = array(); // any additional hidden fields needed, type SfcHidden
	public $hiddenFieldsData = array(); // any data returned from hidden fields
    public $update = false; // load data to be updated
    public $name = ""; // name of form used in error handling
	/** @var string defualt charcter set */
	public $charSet = "UTF-8";

    // Submission field config
	public $formSubmitVar = "SFC_Submit"; // hidden field to detect form submission, contains submission token if enabled.
	public $checkToken = true;
    public $submitted = false; // form has been submitted
    public $tokenExpired = false; // taken too long to submit form
    public $tokenTimeoutError = "The form has expired, please submit the form again"; // token time out error
    public $tokenDataCheckError = 'Token data type incorrect, possible hack attempt, data:%s:'; // token returns with wrong data type
	public $dateFormat = "dd/mm/yyyy"; // format used for dates
	public $timeFormat = "hh:mm:ss"; // format used for time
	public $fieldDataChecks = array(); // array of data check types

    // Results after checking
    public $errorResult = array(); // array error strings from fields
    public $error = false; // formCheck produced an error
	/** automatic re-display of form on error
	 * @var bool  */
    public $errorAutoFormDisplay = true;
    public $errorOutput = ""; // form error output result
    public $formCheck = ""; // function to check general form error, $this needs to be passed by reference to return a result
	public $moreCheck = ""; // additional variable or array sent to form check function
    public $errorTextHack = 'Possible hack attempt, hidden field %s came back with wrong data type, data:%s:'; // error for  invalid hidden field return
    public $errorHack = false; // flags a hack attempt
    public $hackFunction = ""; // function called on hack attempt detection
	/** @var bool display form element errors localy to that element and not in the global list, switches the error strings */
	public $errorsLocal = false;
	/** @var string error string for invalid data input */
    public $errorTextData = '%s has a problem, %s';
	/** @var array Error strings for errors returned by the data checking functions and placed in global errors */
	public $errorTextDataStrings = array(
					1 => "invalid characters have been entered, valid characters are: %s, please modify your entry to reflect this. Thanks!",
					2 => "the URL is too long",
					3 => "the URL needs a period",
					4 => "the URL does not exist",
					5 => "this Email is too long, please check and shorten it. Thanks!",
					6 => "this email needs an @ and a period, please check and add them. Thanks!",
				);
	/** @var array Error strings for errors returned by the data checking functions and placed in local errors */
	public $errorTextDataStringsLocal = array(
					1 => "Invalid characters have been entered, valid characters are: %s, please modify your entry to reflect this. Thanks!",
					2 => "This URL is too long",
					3 => "This URL needs a period",
					4 => "This URL does not exist",
					5 => "This Email is too long, please check and shorten it. Thanks!",
					6 => "This email needs an @ and a period, please check and add them. Thanks!",
				);
	/**
	 * data checking object
	 * @var CheckData */
	public $checkData;
	/**
	 * flags a data error
	 * @var bool */
    public $errorData = false;
	/**
	 * error string for invalid element configuration 
	 * @var string */
    public $errorTextInvalidElement = 'Invalid element type: %s, element name: %s';
	/** @var array default error strings used by form elements and placed in global error list */
    public $elementErrorStrings = array(
			"required" => "%s is a required field, please enter a value. Thanks!", // string used for an empty required element
			"maxChar" => "Please ensure that %s has less than %s characters. Thanks!",
			"minChar" => "Please ensure that %s has at least %s characters. Thanks!",
			"errorVal" => "Value returned by %s not in valid results"
		);
	/** @var array default error strings used by form elements and placed in local error list */
    public $elementErrorStringsLocal = array(
			"required" => "This is a required field, please enter a value. Thanks!", // string used for an empty required element
			"maxChar" => 'Please ensure that this has less than %2$s characters. Thanks!',
			"minChar" => 'Please ensure that this has at least %2$s characters. Thanks!',
			"errorVal" => "Value returned by this is not in valid results"
		);

    // data for sql generation
    public $dataTable = ""; // name of data table associated with the form
    public $whereCondition = ""; // condition to retrieve or update the data with ? replacement fields
    public $whereValues = array(); // array of values for where condition replaceable filelds
    public $alsoAdd = array(); // array of data to be added to insert or update query, not added to form
    public $resultQuery; // string - resulting query from form with ? parameters.
    public $resultData = array(); // array - array of data to use in replacement parameters
    public $saveFields = array(); // fields and values to be saved from the form
    public $selectFields = array(); // array of fields to be selected for the form
    public $ignorElements = " submit reset image button startrow endrow colrow"; // elements to be ignored when creating queries and checking data

    public $quote = false; // quote fieled names
    public $quoteStart = '"';
    public $quoteEnd = '"';

	// paged form configuration
	public $pageNumber = 0; // page number currently displayed
	public $noPages = 1; // number of pages in form - not used for now
	public $pageName = array(0 => "First Page"); // names of pages

	// formatting properties
	public $buildForm = false; // auto generate form using tables, else use template
	public $templateRoutine = ""; // externally defined template routine, uses eval and passes the form fields as an array. Should return the xhtml for the resulting form.
	public $tDataAdditional = array(); // additional data for the template
	public $tData; // array of form fields data for the template routine
	public $tpl; // general template
	public $tplElement = "content"; // element in the main template into which the form is put
	public $tplf = ""; // form template name
	public $tplp = ""; // preview template name
	/**
	 * generate form using template 
	 * @var bool
	 */
	public $generateForm = true;

    // preview configuration
    public $preview = false; // do a preview for the form result
    public $previewRoutine=""; // routine used to preview results of the form, uses eval and passes the form fields as an array. Should return the xhtml for the resulting form.
    public $previewBackName = "previewBack"; // name of back button on preview form
    public $previewSaveName = "previewSave"; // name of save button on preview form
    public $previewBack = false; // back pressed on preview form
    public $previewSave = false; // save pressed on preview form

    // formatting for form elements
    public $id = "form"; // id assigned to the form for style sheets etc.
    public $fclass = "";
    public $tableTabIndex = 0; // automatic tab index for elements
	public $tableTabIndexAuto = false; // automatically create a tabindex
    public $ldir = ""; // language direction, can be ltr or rtl
    public $lang = ""; // language code for element
    public $style = ""; // local styling for form
    public $reqChar = "* %s"; // formatting string to put caracter on required strings
    public $errorClass = "SFCError"; // Class used on erroring field descriptions.
    public $hiddenClass = "sfcHidden"; // class to apply to hidden fields
	/** Add placeholder elements using description
	 * @var bool  */
	public $addPlaceholder = false;

	// parameters for the auto generated form
	public $formTitle = ""; // Title and text to be printed above the form
	public $formFooter = ""; // text to be printed after the form
	public $genTextFormat = "<p>%s</p>";
	public $threeCols = false; // put in help text as third column
	public $startCont = '<table>'; //start of ofrmatting structure
	public $startRow = '<tr><td>'; // xhtml on beginning of line
	public $startRowOdd = '<tr class="oddRow"><td>'; // xhtml on beginning of line for odd row
    public $startRowEven = '<tr class="evenRow"><td>'; // xhtml on beginning of line for even row
    public $lableRows = false; // lable odd and even rows using startRowOdd and StartRowEven
    public $rowEven = false; // row is even
	public $sepCol1 = '</td><td>'; // column seperator between 1 and 2
	public $sepCol2 = '</td><td>'; // column seperator between 2 and 3 if there is a third column
	public $endRow = '</td></tr>'; // end of line
    public $colRow = '</td><td>';
	public $endCont = '</table>'; // end of formatting structure if used

    public $elementProperties; // properties available in element class

	public $debug = false;
	public $debugResults = array();

	/** @var bool attempt to translate the text */
	private static $translate = false;
	/** @var bool disable translation for this form object */
	public $translateDisable = false;
	/** @var object translator object use to translate strings */
	private static $tranlator;
	/** @var string name of method used in tranlation object */
	private static $translateMethod = 't';
	/**
	 * Function to generate form submission tokens
	 * @var string
	 */
	private static $tokenMaker = '\w34u\ssp\SSP_Token';
	/**
	 * Function to check token submitted by the form
	 * @var string
	 */
	private static $tokenChecker = '\w34u\ssp\SSP_TokenCheck';
	/**
	 * Data type of toekn submitted
	 * @var string
	 */
	private static $tokenDataType = 'hex';
	
   	/**
	 * Form constructor
	 * @param string $action
	 * @param string $dataTable
	 * @param string $name 
	 */
    public function  __construct($action, $dataTable, $name) {
        // constructor for class
		
		// set up check data object
		$this->checkData = new CheckData();
		
		// target of the form
        $this->action=$action;
		$this->dataTable = $dataTable;
        // set up token functions if they exist
        if((self::$tokenMaker == "" and self::$tokenChecker != "") or (self::$tokenMaker != "" and self::$tokenChecker == "")){
            SSP_error("SFC form, Token functions improperly defined");
        }
        //
        $this->elementProperties = get_class_vars('\w34u\ssp\SfcFe');
		
		// check for valid form name
		if(!isset($name) or trim($name) == "" or $this->dataCheck($name, "password", "Form name") !== true){
			SSP_error("Please give form '$name' a valid name, a-z, A-Z, 0-9 no spaces");
		}
		else{
			$this->name = $name;
		}
    }
	
	/**
	 * adds a form element to the form
	 * @param string $type - element type
	 * @param string $name - element name
	 * @param string $description - description for label etc.
	 * @param string/array $data - data for initialise, dropdown etc.
	 */
    public function fe($type, $name, $description="", $data=""){
        if(!isset($this->elements[$name])){
            // if element does not exist already create it
            $this->currentElement = $name;
            $this->elements[$name] = new SfcFe($type, $name, $description, $data);
        }
        else{
            SSP_error("SfcForm ". $this->name. ":". sprintf($this->t($this->errorElement), $name, $this->dataTable));
        }
    }

	/**
	 * set parameters for a form element
	 * @param string $params - string of parameters to be set name1=value, name2=value, etc.
	 * @param type $name - name of element, if "" current element will be used
	 */
    public function fep($params, $name=""){
        if($name != ""){
            $elementName = $name;
        }
        else{
            $elementName = $this->currentElement;
        }

        if(is_string($params)){
        	if(strlen(trim($params)) > 0){
				$parameters = explode(",", $params);
				foreach($parameters as $entry){
						if(strlen(trim($entry)) > 0){
						$param = explode("=", $entry);
						$param[0] = trim($param[0]);
						$param[1] = trim($param[1]);
						if($param[1] == "true"){
							$param[1] = true;
						}
						elseif($param[1] == "false"){
							$param[1] = false;
						}
						if(array_key_exists($param[0], $this->elementProperties)){
							$this->elements[$elementName]->$param[0] = $param[1];
						}
						else{
							SSP_error("SfcForm::fep $this->name, Form element property $param[0] does not exist in $elementName");
						}
					}
				}
			}
        }
        else{
            SSP_error("SfcForm::fep $this->name, Need a string of data for $elementName");
        }
    }
	
	/**
	 * Set a field parameter value
	 * @param string $paramName - name of parameter
	 * @param any $value - value to be set
	 * @param string $fieldName - name of filed to be set
	 */
	public function setParam($paramName, $value, $fieldName=""){
        if($fieldName != ""){
            $elementName = $fieldName;
        }
        else{
            $elementName = $this->currentElement;
        }
		
		$this->elements[$elementName]->$paramName = $value;
	}

	public function getField($elementName){
		// Retrieves the value submitted by the form for the element
		//
		// paramaters
		//	$elementName - string - name of element who's data is needed

		if(array_key_exists($elementName, $this->elements)){
			$result = $this->elements[$elementName]->field;
		}
		else{
			SSP_error("SfcForm::getField $this->name: Element $elementName does not exist");
		}
		return($result);
	}

	/**
	 * check if a form element exists
	 * @param string $elementName - name of element
	 * @return bool - true if exists
	 */
	public function existsField($elementName){
		return(array_key_exists($elementName, $this->elements));
	}

	public function getHidden($hiddenFieldName){
		if(array_key_exists($hiddenFieldName, $this->hiddenFields)){
			$result = $this->hiddenFieldsData[$hiddenFieldName];
		}
		else{
			SSP_error("SfcForm::getField $this->name: Hidden field $hiddenFieldName does not exist");
		}
		return($result);
	}

	public function setField($elementName, $value){
		// Sets the value of a form element
		//
		// paramaters
		//	$elementName - string - name of element who's data is needed

		if(array_key_exists($elementName, $this->elements)){
			$this->elements[$elementName]->field = $value;
		}
		else{
			SSP_error("SfcForm::setField $this->name: Element $elementName does not exist");
		}
	}

	public function setError($elementName, $value){
		// Sets the error for a form element
		//
		// paramaters
		//	$elementName - string - name of element who's data is needed
		//	$value - string - error to be set

		if(array_key_exists($elementName, $this->elements)){
			$this->elements[$elementName]->addError($value);
			$this->error = true;
		}
		else{
			SSP_error("SfcForm::setField $this->name: Element $elementName does not exist");
		}
	}

	public function tda($name, $value=""){
		// adds template addtional data

		$this->tDataAdditional[$name] = $value;
	}

    /**
	 * Creates a form from the definition
	 * The following data is used:-
	 *  elements - generates a form based on this array.
	 *	data - if in update mode adds in data from this array
	 *	elementSelections - array of arrays for particular select elements
	 *	errorResult - array of errored fields
	 *  hiddenFields - additional hidden fields
	 * @param bool $update - loads form data
	 * @return string - html for the form
	 */
	public function create($update=false) {
        $this->update = $update; // set if data availabe to populate the form
        if($update and !is_array($this->data)){
        	SSP_error("SFC form create $this->name: no data supplied to populate form");
        }
        // add any errors
        if($update){
			if($this->submitted){
				foreach($this->elements as $el){
					$fieldErrorListName = $el->name. "ErrorList";
					if($el->error){
						if(!$this->errorsLocal){
							$this->errorResult = array_merge($this->errorResult, $el->errorText);
							$this->tda($fieldErrorListName, "");
						}
						else{
							$this->tda($fieldErrorListName, $this->printErrors($el->errorText, true));
						}
					}
					else{
						$this->tda($fieldErrorListName, "");
					}
				}
			}
			else{
				// Get data from other source
				foreach($this->elements as $key => $el){
					if($el->dbField !== ""){
						if(isset($this->data[$el->dbField])){
							$this->elements[$key]->field = $this->data[$el->dbField];
						}
					}
					else{
						if(isset($this->data[$el->name])){
							$this->elements[$key]->field = $this->data[$el->name];
						}
					}
				}
			}
        }
		else{
			foreach($this->elements as $el){
				$fieldErrorListName = $el->name. "ErrorList";
				$this->tda($fieldErrorListName, "");
			}
		}
        $this->tDataAdditional["errorList"] = $this->printErrors();
		if($this->buildForm){
			if($this->formTitle != ""){
				$xhtml = $this->formTitle;
			}
			else{
				$xhtml = "";
			}
			if(trim($this->tDataAdditional["errorList"]) != ""){
				$xhtml .= $this->tDataAdditional["errorList"];
			}
			$xhtml .= "\n". '<form action="'.$this->action . '" ';
			$xhtml .= 'method="' . $this->method . '" id="' . $this->id . '" ';
			if($this->enctype != "") {
				$xhtml .= 'enctype="' . $this->enctype . '" ';
			}
			if($this->formT != "") {
				$xhtml .= 'target="' . $this->formT . '" ';
			}
			if($this->fclass != ""){
				$xhtml .= 'class="'. $this->fclass. '" ';
			}
			if($this->lang != ""){
				$xhtml .= 'lang="'. $this->lang. '" ';
			}
			if($this->ldir != ""){
				$xhtml .= 'dir="'. $this->ldir. '" ';
			}
			if($this->script != "") {
				$xhtml .= $this->script . " ";
			}
			$xhtml .= "> \n";

			// start form elements container
			if($this->startCont != ""){
				$xhtml .=$this->startCont."\n";
			}
		}
		else{
			$this->tData = array(
				"formAction" => $this->action,
				"formMethod" => $this->method
				);
		}

        // generate fields from elements adding data if necessary
        foreach($this->elements as $el) {
            $this->tableTabIndex++;
			
			// check the data type of field is valid
			if(!$this->checkData->isType($el->dataType)){
				SSP_error("Invalid data type {$el->dataType} specified in element $el->name");
			}

            // toggle even/odd rows
            if($this->rowEven){
                $this->rowEven = false;
            }
            else{
                $this->rowEven = true;
            }

            switch(strtolower($el->type)) {
                case "text":
				case "password":
					if($this->buildForm){
						$xhtml .= $this->startRow($el);
						$xhtml .= $this->elText($el);
						$xhtml .= $this->endRow($el);
					}
					else{
						$this->tData($el, $this->elText($el));
					}
                break;
				case "check":
					if($this->buildForm){
						$xhtml .= $this->startRow($el);
						$xhtml .= $this->elCheck($el);
						$xhtml .= $this->endRow($el);
					}
					else{
						$this->tData($el, $this->elCheck($el));
					}
                break;
                case "select":
					if($this->buildForm){
						$xhtml .= $this->startRow($el);
						$xhtml .= $this->elSelect($el);
						$xhtml .= $this->endRow($el);
					}
					else{
						$this->tData($el, $this->elSelect($el));
					}
				break;
                case "textarea":
					if($this->buildForm){
						$xhtml .= $this->startRow($el);
						$xhtml .= $this->elTextArea($el);
						$xhtml .= $this->endRow($el);
					}
					else{
						$this->tData($el, $this->elTextArea($el));
					}
                break;
                case "radio":
					if($this->buildForm){
						$xhtml .= $this->startRow($el);
						$radio = $this->elRadio($el);
						$xhtml .= $radio[0];
						$xhtml .= $this->endRow($el);
					}
					else{
						$this->tData($el, $this->elRadio($el));
					}
                break;
                case "startrow":
                    // start of a line for holder
					if($this->buildForm){
						$xhtml .= $this->startRow;
					}
                break;
                case "endrow":
                    // end of line for holder
					if($this->buildForm){
						$xhtml .= $this->endRow;
					}
                break;
                case "colrow":
                    // column break for container
					if($this->buildForm){
						$xhtml .= $this->colRow;
					}
                break;
                case "submit":
                    // submit button
					if($this->buildForm){
						$xhtml .= $this->elSubmit($el);
					}
					else{
						$this->tData($el, $this->elSubmit($el));
					}
                break;
                case "reset":
                    // reset button
					if($this->buildForm){
						$xhtml .= $this->elReset($el);
					}
					else{
						$this->tData($el, $this->elReset($el));
					}
                break;
                case "button":
					if($this->buildForm){
						$xhtml .= $this->elButton($el);
					}
					else{
						$this->tData($el, $this->elButton($el));
					}
                break;
                case "image":
					if($this->buildForm){
						$xhtml .= $this->elImage($el);
					}
					else{
						$this->tData($el, $el, $this->elImage($el));
					}
                break;
                case "file":
					if($this->buildForm){
						$xhtml .= $this->startRow($el);
						$xhtml .= $this->elFile($el);
						$xhtml .= $this->endRow($el);
					}
					else{
						$this->tData($el, $this->elFile($el));
						if(isset($this->fileObjects[$el->name]) and !$this->submitted){
							if($this->preview and !$this->fileObjects[$el->name]->preview){
								SSP_error("SfcForm::create $this->name: preview information needed for $el->name");
							}
							// check for a file upload object and an existing file from db
							if($filePath = $this->fileObjects[$el->name]->getDisplay($this->data)){
								$this->tDataAdditional[$el->name. "Display"] = $filePath;
								$this->addHidden($el->name. "Display", $this->fileObjects[$el->name]->fileName);
							}
						}
					}
                break;
                default:
                    // throw error for invalid element
                    SSP_error("SfcForm::create $this->name: ". sprintf($this->t($this->errorTextInvalidElement), $el->type, $el->name));
            }
        }
		if($this->buildForm){
			$xhtml .= $this->endForm();
			if($this->formFooter != ""){
				$xhtml .= $this->formFooter;
			}
		}
		else{
			$this->tData["formHidden"] = $this->hiddenFields();
			if(is_array($this->tDataAdditional)){
				$this->tData = array_merge($this->tData, $this->tDataAdditional);
			}
			if($this->generateForm){
			if($this->templateRoutine != ''){
				$xhtml = call_user_func($this->templateRoutine, $this->tData);
			}
			else{
				$xhtml = $this->formTemplate();
				}
			}
			else{
				$xhtml = "";
			}
		}

        return($xhtml);
    }

    /**
	 * Default routine to generate html for the form
	 * @return string html for the form and any wrapping template
	 */
	private function formTemplate(){
		// routine to apply templates to simple forms

		if((is_object($this->tplf) and strcmp(strtolower(get_class($this->tplf)), "ssp_template") == 0) or $this->tplf != ""){
			if(is_string($this->tplf)){
				// template name supplied
			$form = new Template($this->tData, $this->tplf, false);
			}
			else{
				// template object supplied
				$form = $this->tplf;
				$form->restart($this->tData);
			}
			// prevent escaping of html form fields
			foreach($this->elements as $key => $el){
				$form->ne($key);
				$form->ne($key. "Desc");
				$form->ne($key. "ErrorList");
				if($el->type == "radio"){
					$radioElementNumber = 0;
					foreach($el->data as $dataValue){
						$form->ne($key. "_". $radioElementNumber);
						$radioElementNumber ++;
					}
				}
			}
			$form->ne("errorList");
			$form->ne("formAction");
			$form->ne("formHidden");
			
			$formResult = $form->output();

			if(is_object($this->tpl)){
				$this->tpl->display = false;
				$this->tpl->setData($this->tplElement, $formResult);
				$this->tpl->ne($this->tplElement);
				$output = $this->tpl->output();
			}
			else{
				$output = $formResult;
			}
		}
		else{
			SSP_error("SfcForm::formTemplate $this->name: Please supply a form template name or object in ->tplf");
		}

		return($output);
    }

	private function tData($el, $xhtml){
		// Creates elements for the template data array
		//
		// parameters
		//	$el - object - form element
		//	$xhtml - string - xhtml for the main form field

		if($el->encap){
			$this->tData[$el->name] = $this->description($el, $xhtml);
		}
		else{
			if(is_array($xhtml)){
				foreach($xhtml as $key => $string){
					if($key == 0){
						$this->tData[$el->name] = $string;
					}
					else{
						$this->tData[$el->name. "_". ($key-1)] = $string;
					}
				}

			}
			else{
				$this->tData[$el->name] = $xhtml;
			}
			$this->tData[$el->name. 'Desc'] = $this->description($el);
		}
	}

    private function startRow($el){
        // returns xhtml to be displayed before the form element
        if(!$this->lableRows){
            $xhtml = $this->startRow;
        }
        else{
            if($this->rowEven){
                $xhtml = $this->startRowEven;
            }
            else{
                $xhtml = $this->startRowOdd;
            }
        }
        $xhtml .= $this->description($el);
        $xhtml .= $this->sepCol1;
		if($el->genText != ""){
			$xhtml .= sprintf($this->genTextFormat, $el->genText);
		}
        return($xhtml);
    }

    private function endRow($el){
        // returns xhtml to be displayed after the form element
        $xhtml = "";
        if($this->threeCols){
            $xhtml .= $this->sepCol2. $el->helpText;
        }
        $xhtml .= $this->endRow. "\n";
        return($xhtml);
    }

    /**
	 * Generate text input box
	 * @param SfcFe $el - form element
	 * @return string
	 */
	private function elText($el){
        // Text and password form elements
        $xhtml = '<input type="'. $el->type. '" ';
		// sort out placeholder text
		if($el->placeholder !== ''){
			$xhtml .= 'placeholder="'. $this->t($el->placeholder). '" ';
		}
		elseif($this->addPlaceholder){
			$xhtml .= 'placeholder="'. $this->t($el->description). '" ';
		}
        if($el->width != ""){
            $xhtml .= 'size="'. $el->width. '" ';
        }
        if($this->update and $el->type == "text") {
            if($el->load) {
                $xhtml .= 'value="' . $this->encode($el->field) . '" ';
            }
        }
        elseif($el->deflt != "" and $el->type == "text") {
            $xhtml .= 'value="' . $el->deflt .'" ';
        }
        if($el->maxLength != "") {
            $xhtml .= 'maxlength ="' . $el->maxLength . '" ';
        }
        if($el->readonly and $el->type == "text") {
            $xhtml .= "readonly ";
        }
        $xhtml .= $this->comPars($el, "sfcText");
        $xhtml .= '/>'. "\n";
        return($xhtml);
    }

    private function elCheck($el){
        // Checkbox form element
        if(!is_array($el->data)){
            exit("Checkbox ".$el->name." needs array as data");
        }
		reset($el->data);
		$chckValue = next($el->data);
        $xhtml = '<input type="checkbox" ';
        $xhtml .= 'value="'. $chckValue. '" ';
        if($this->update){
            if($el->load) {
                if($el->field == $chckValue){
                    $xhtml .= 'checked="checked" ';
                }
            }
        }
        elseif($el->deflt){
            $xhtml .= 'checked="checked" ';
        }
        $xhtml .= $this->comPars($el, "sfcCheck");
        $xhtml .= '/>';
        return($xhtml);
    }

    /**
	 * Genearate a radio button set
	 * @param SfcFe $el - form element
	 * @return string - xhtml return
	 */
	private function elRadio($el){
        // Checkbox form element
        if(!is_array($el->data)){
            exit("Radio Buttons ".$el->name." needs array as data");
        }
		$xhtml = "";
		$xhtmlTotal = "";
		$return = array();
		$idCount = 0;
		foreach($el->data as $key => $description){
			if($el->radioDesc){
				$xhtml .= '<label';
				// error handling
				if($el->lClass !=""){
					$xhtml .= ' class="'. $el->lClass;
					if($el->error) {
						$xhtml .= ' '. $this->errorClass;
					}
					$xhtml .= '"';
				}
				elseif($el->error) {
					$xhtml .= ' class="'. $this->errorClass. '"';
				}
				$xhtml .= '>';
				if($el->textBefore){
					$xhtml .= $this->t($description);
				}
			}
			if($el->id != ""){
				$radioId = $el->id. "_". $idCount;
			}
			else{
				$radioId = $el->name. "_". $idCount;
			}
			$xhtml .= '<input type="radio" id="'. $radioId. '" title="'. strip_tags($el->description). ', '. strip_tags($description). '" ';
			$xhtml .= 'value="'. $key. '" ';
			if($this->update){
				if($el->load) {
					if(trim($el->field) != "" and $el->field == $key){
						$xhtml .= 'checked="checked" ';
					}
				}
			}
			elseif($el->deflt != "" and $el->deflt == $key){
				$xhtml .= 'checked="checked" ';
			}
			$xhtml .= $this->comPars($el, "sfcRadio");
			$xhtml .= '/>';
			if($el->radioDesc){
				if(!$el->textBefore){
					$xhtml .= $this->t($description);
				}
				$xhtml .= '</label>';
			}
			if(strcmp($el->orientation, "vertical") == 0){
				$xhtml .= "<br />";
			}
			$xhtmlTotal .= $xhtml;
			$idCount ++;
			$return[$idCount] = $xhtml;
			$xhtml = "";
		}
		$return[0] = $xhtmlTotal;
        return($return);
    }

    /**
	 * Generate a select dropdown
	 * Data structure
	 * Simple dropdown - array("option1Value" => "option1Text", "option2Value" => "option2Text")
	 * Dropdown with option groups
	 * array(
	 *	object(type = "optgroupt", text => "optgroup1Text", options => array("option1Value" => "option1Text", "option2Value" => "option2Text"),
	 *	object(type = "optgroupt", text => "optgroup2Text", options => array("option3Value" => "option3Text", "option4Value" => "option4Text")
	 * )
	 * Adding attributes to options and optgroups
	 * If an option is an object but does not have type == optgroup the text property is used as the text and the other
	 * properties are put in as attributes
	 * @param SfcFe $el - select element
	 * @return string 
	 */
	private function elSelect($el){
        // Selection dropdown or area form element
        $xhtml = '<select ';
        if($el->lines != ""){
            $xhtml .= 'size="'. $el->lines. '" ';
        }
        $xhtml .= $this->comPars($el, "sfcSelect");
        $xhtml .= '>'. "\n";
        if($el->useExternal) {
            $selections = $this->elementSelections[$el->name];
        }
        else {
            $selections = $el->data;
        }
        if(!is_array($selections)){
            echo SSP_error("SfcForm::elSelect $this->name: Need selections array for $el->name");
        }
		/*
        if(!$this->update and (trim($el->deflt) == "" or !array_key_exists($el->deflt, $selections))){
            // set selected to first data element
            reset($selections);
            $el->deflt = key($selections);
        }
		 */
        if($el->load){
        	$currentValue = $el->field;
        }
        else{
        	$currentValue = "";
        }
		$xhtml .= $this->elSelectOptions($el, $selections, $currentValue);
        $xhtml .= "</select>";
        return($xhtml);
    }
	
	/**
	 * Recursive routine to generate select options and optgroups
	 * @param SfcFe $el - select element
	 * @param array $selections - array of select selections
	 * @param string $currentValue - current value of selection
	 * @return string - resulting xhtml
	 */
	private function elSelectOptions($el, $selections, $currentValue){
		$xhtml = "";
        foreach($selections as $value => $attributes) {
			// work through option groups
			if(is_object($attributes) and isset($attributes->type) and $attributes->type == "optgroup"){
				if(!isset($attributes->text)){
					echo SSP_error("SfcForm::elSelect {$this->name}: Invalid options group in {$el->name}, needs text property");
				}
				if(!isset($attributes->options) or !is_array($attributes->options)){
					echo SSP_error("SfcForm::elSelect {$this->name}: Invalid options group in {$el->name}, optgroup {$attributes->text} needs array of options property");
				}
				$xhtml .= '<optgroup label="'. $this->t($attributes->text). '"';
				// add optgroup attributes
				$attributesArray = get_object_vars($attributes);
				foreach($attributesArray as $attribute => $value){
					if(strpos('type text options', $attribute) === false){
						$xhtml .= ' '. $attribute. '="'. $value. '"';
					}
				}
				$xhtml .= '>'. "\n". $this->elSelectOptions($el, $attributes->options, $currentValue). '</optgroup>'. "\n";
			}
			else{
				// standard array of options
				$xhtml .='<option value="'.$value.'"';
				if(is_string($attributes) or is_numeric($attributes)){
					// attributes are either a peice of text or a number
					$text = $attributes;
				}
				else{
					if(is_object($attributes)){
						$attributes = get_object_vars($attributes);
					}
					if(!isset($attributes['text'])){
						echo SSP_error("SfcForm::elSelect {$this->name}: Invalid option {$value} in {$el->name}, needs text property");
					}
					foreach($attributes as $attribute => $attributeValue){
						if($attribute == "text"){
							$text = $attributeValue;
						}
						else{
							if(!is_array($attributeValue)){
								$xhtml .= ' '. $attribute. '="'. $attributeValue. '"';
							}
						}
					}
				}
				if($this->update){
					if(strcmp($value, $currentValue) == 0){
						$xhtml .= ' selected="selected"';
					}
				}
				elseif(strcmp($el->deflt, $value) == 0){
					$xhtml .= ' selected="selected"';
				}
				$xhtml .= ">". $this->t($text). "</option>\n";
			}
        }
		return($xhtml);
	}

    /**
	 * Create a text element
	 * @param SfcFe $el - element object
	 * @return string - html result
	 */
	private function elTextArea($el){
        // Textarea form element
        $xhtml = '<textarea ';
		// sort out placeholder text
		if($el->placeholder !== ''){
			$xhtml .= 'placeholder="'. $this->t($el->placeholder). '" ';
		}
		elseif($this->addPlaceholder){
			$xhtml .= 'placeholder="'. $this->t($el->description). '" ';
		}
        if($el->width != ""){
            $xhtml .= 'cols="'. $el->width. '" ';
        }
        if($el->lines != ""){
            $xhtml .= 'rows="'. $el->lines. '" ';
        }
        if($el->readonly) {
            $xhtml .= "readonly ";
        }
        $xhtml .= $this->comPars($el, "sfcTextArea");
        $xhtml .= '>';
        if($this->update) {
            if($el->load) {
                $xhtml .= $this->encode($el->field);
            }
        }
        elseif($el->deflt != "") {
            $xhtml .= $el->deflt;
        }
        $xhtml .= "</textarea>";
        return($xhtml);
    }

    private function elSubmit($el){
        // create submit button xhtml
        $xhtml = '<input type="submit" value="'. $this->t($el->description). '" ';
        $xhtml .= $this->comPars($el, "sfcSubmit");
        $xhtml .= '/>';
        return($xhtml);
    }

    private function elReset($el){
        // create reset button xhtml
        $xhtml = '<input type="reset" value="'. $this->t($el->description). '" ';
        $xhtml .= $this->comPars($el, "sfcReset");
        $xhtml .= '/>';
        return($xhtml);
    }

    private function elButton($el){
        // create button xhtml
        $xhtml = '<button type="'. $el->buttonType. '" value="'. $el->value. '" ';
        $xhtml .= $this->comPars($el, "sfcButton");
        $xhtml .= '>'. $this->t($el->description). '</button>';
        return($xhtml);
    }

    private function elImage($el){
        // create image button xhtml
        $xhtml = '<input type="image" value="'. $el->value. '" src="'. $el->image. '" ';
        $xhtml .= $this->comPars($el, "sfcImage");
        $xhtml .= '/>';
        return($xhtml);
    }

    /**
	 * Generate file element
	 * @param SfcFe $el - form element
	 * @return string - xhtml result
	 */
	private function elFile($el){
        // function to create file download form field
        $xhtml = '<input type="file" ';
		// sort out placeholder text
		if($el->placeholder !== ''){
			$xhtml .= 'placeholder="'. $this->t($el->placeholder). '" ';
		}
		elseif($this->addPlaceholder){
			$xhtml .= 'placeholder="'. $this->t($el->description). '" ';
		}
        if($el->width != ""){
            $xhtml .= "width = '".$el->width."' ";
        }
        if($el->accept != ""){
            $xhtml .= "accept = '".$el->accept."' ";
        }
        $xhtml .= $this->comPars($el, "sfcFile");
        $xhtml.= "/>";
        return($xhtml);
    }

    private function endForm(){
        // terminate holder, place hidden fields and finish form
        $xhtml = $this->endCont. "\n";
        $xhtml .= $this->hiddenFields();
        $xhtml .= "</form>\n";
        return($xhtml);
    }
	
    /**
	 * Do common parameters for the form element
	 * @param SfcFe $el - forma element
	 * @param string $class - classes added to the element
	 * @return string - xhtml for the element 
	 */
    private function comPars($el, $class){
        // returns the common parameters used by most form elements and closes the tag
        // name, id, tabindex, accesskey, script, title, style, class, dir, lang

        $xhtml = 'name="'. $el->name. '" ';
		if($el->type != "radio"){
			if($el->id != ""){
				$xhtml .= 'id="' .$el->id. '" ';
			}
			else{
			$xhtml .= 'id="' .$el->name. '" ';
			}
		}
        if($el->tabIndex != "") {
            $xhtml .= 'tabindex="' . $el->tabIndex . '" ';
        }
        else {
			if($this->tableTabIndexAuto){
				$xhtml .= 'tabindex="' . $this->tableTabIndex . '" ';
			}
        }
        if($el->accessKey != "") {
            $xhtml .= 'accesskey="' . $el->accessKey . '" ';
        }
        if($el->script != "") {
            $xhtml .= $el->script . " ";
        }
		if($el->type != "radio"){
			if($el->helpText != ""){
				$xhtml .= 'title="'. strip_tags($this->t($el->helpText)). '" ';
			}
			else{
				$xhtml .= 'title="'. strip_tags($this->t($el->description)). '" ';
			}
		}
        if($el->style != ""){
            $xhtml .= 'style="'. $el->style. '" ';
        }
        $xhtml .= 'class="'. $class;
        if($el->elClass != ""){
            $xhtml .=  " ". $el->elClass;
        }
		if($el->error){
			$xhtml .= " ". $this->errorClass;
		}
/*		if($el->dataType != ""){
			$xhtml .= " sfc". ucfirst($el->dataType);
		}*/
        $xhtml .= '" ';
        if($el->lang != ""){
            $xhtml .= 'lang="'. $el->lang. '" ';
        }
        if($el->ldir != ""){
            $xhtml .= 'dir="'. $el->ldir. '" ';
        }
        if($el->disabled) {
            $xhtml .= "disabled ";
        }
        return($xhtml);
    }

    private function hiddenFields(){
        // create hidden fields for the form
        // form submission check field
        if($this->checkToken and self::$tokenMaker != ""){
            $tokenValue = call_user_func(self::$tokenMaker, $this->name);
        }
        else{
            $tokenValue = "1";
        }
        $xhtml = '<input type="hidden" name="'. $this->formSubmitVar. '" value="'. $tokenValue. '" class="'. $this->hiddenClass. '" />';
        if(is_Array($this->hiddenFields)){
            foreach($this->hiddenFields as $key => $hidden){
            	if(is_object($hidden) and strcmp(strtolower(get_class($hidden)), "w34u\ssp\sfchidden") == 0){
            		if($hidden->elClass != ""){
            			$class = ' class="'. $hidden->elClass. '"';
            		}
            		else{
            			$class = ' class="'. $this->hiddenClass. '"';;
            		}
            		$xhtml .= '<input type="hidden" name="'. $hidden->name. '" id= "'. $hidden->name. '" value="'. $hidden->data. '"'. $class. ' />';
            	}
            	else{
            		SSP_error("SfcForm::hiddenFields $this->name: Hidden field $key not of SfcHidden type");
            	}
            }
        }
        return($xhtml);
    }

    public function addHidden($name, $value, $dataType="", $elClass=""){
		if($dataType != "" and !$this->checkData->isType($dataType)){
			SSP_error("Invalid data type $dataType for hidden field $name");
		}
    	$this->hiddenFields[$name] = new SfcHidden($name, $value, $dataType, $elClass);
    }

    public function setPreview($previewFunction, $backButtonName="", $saveButtonName=""){
    	// set up the form to do a preview of data before saving
    	//
    	// parameters
    	//	$previewFunction - string - name of the function used for previewing
    	//	$backButtonName - string - name of button used to go back to edit
    	//	$saveButtonName - string - name of button used to save

    	$this->preview = true;
    	$this->previewRoutine = $previewFunction;
    	if($backButtonName != ""){
    		$previewBackName = $backButtonName;
    	}
    	if($saveButtonName != ""){
    		$previewSaveName = $saveButtonName;
    	}
    }

    public function previewForm(){
    	// preview data submitted by form

		$tData = array(
			"formAction" => $this->action,
			"formMethod" => $this->method
		);
    	$tData["formHidden"] = "";
    	foreach($this->elements as $el){
    		if(!strpos($this->ignorElements,$el->type) and $el->sql === true){
				if($el->useExternal) {
					$selections=$this->elementSelections[$el->name];
				}
				else {
					$selections=$el->data;
				}
				if(!is_array($selections)){
    				$tData[$el->name] = $el->field;
    			}
    			else{
    				$tData[$el->name] = $selections[$el->field];
    			}
    			$tData[$el->name. "Desc"] = $el->description;
    			if($el->dataType != "gen"){
    				$tData["formHidden"] .= '<input type="hidden" name="'. $el->name. '" value="'. $el->field. '" />';
    			}
    			else{
    				$tData["formHidden"] .= '<input type="hidden" name="'. $el->name. '" value="'. str_replace('"', "&#034;", $el->field). '" />';
    			}
    		}
    	}
    	$tData["formHidden"] .= $this->hiddenFields();
    	$tData = array_merge($this->tDataAdditional, $tData);
		$xhtml = call_user_func($this->previewRoutine, $tData);
    	return($xhtml);
    }

    /**
	 * Processes a submitted form
	 * returns true on form submit
	 * On failure of form process will re-display the form with errors and call
	 * a hack attempt script if neccesary
	 * @param array $data - form get or post array
	 * @return bool - true if fmorm submitted
	 */
	public function processForm(&$data){
        if($this->isSubmit($data)){
	        $this->formCheck();
            if($this->errorHack and trim($this->hackFunction) != ""){
                call_user_func($this->hackFunction, $this); // call hack function, needs user definition
            }
            if($this->error){
                // re-display form
                if($this->errorAutoFormDisplay){
                	echo $this->create(true);
                }
            }
			$result = $this->submitted;
           if($this->preview and !$this->error){
             	// preview form data
            	if($this->previewBack){
            		// reload form for editing
            		echo $this->create(true);
            		return($result);
            	}
            	elseif($this->previewSave){
            		// return true to main routine
					return($result);
            	}
            	else{
            		// display preview
             		echo $this->previewForm();
            		return($result);
            	}
            }
            else{
				return($result);
			}
        }
    }

    /**
	 * Check the form for data errors
	 * puts any error string in $this->errorResult[],
	 * and sets $this->error
	 * @return bool - return true on error 
	 */
	private function formCheck() {

		// call error check function
		if($this->formCheck != ""){
            call_user_func($this->formCheck, $this);
        }

		if($this->error){
			$error = true;
		}
		else{
			$error = false;
		}
		
		// translate field error strings
		$elementErrorStrings = array();
		if($this->errorsLocal){
			foreach($this->elementErrorStringsLocal as $key => $string){
				$elementErrorStrings[$key] = $this->t($string);
			}
		}
		else{
			foreach($this->elementErrorStrings as $key => $string){
				$elementErrorStrings[$key] = $this->t($string);
			}
		}

        foreach($this->elements as $key => $el){
			if(!strpos($this->ignorElements, $el->type)){
				
				// If no return for check box put the unchecked value into the return
				if($el->type == "check"){
					reset($el->data);
					$checkClear = current($el->data);
					$checked = next($el->data);
					if(trim($el->field) == ""){
						$this->elements[$key]->field = $checkClear;
					}
					else{
						$this->elements[$key]->field = $checked;
					}
				}

                // checks all data elements for valid data
				$dataStatus = $this->dataCheck($el->field, $el->dataType, $el->description, $this->errorsLocal);
				if($this->isError($dataStatus)){
					// Data type error returned
					$this->elements[$key]->addError($dataStatus->error);
				}

                // run element error check with returned data
				$this->elements[$key]->checkField($elementErrorStrings);
				
				if($this->elements[$key]->error){
					$error = true;
				}
			}
		}

        // check hidden fields
        if(is_array($this->hiddenFields)){
            foreach($this->hiddenFields as $hidden){
            	if(isset($this->data[$hidden->name])){
					$status = $this->dataCheck($this->data[$hidden->name], $hidden->dataType, $hidden->name);
					if($this->isError($status)){
						$this->errorResult[] = sprintf($this->t($this->errorTextHack), $hidden->name, $this->encode($this->data[$hidden->name]));
						$this->errorHack = true;
						$error = true;
					}
				}
            }
        }

        $this->error = $error;
		return($error);
    }

	/**
	 * Adds an error to the form error list
	 * @param string $error - error to be added
	 */
	public function addError($error){
		$this->errorResult[] = $error;
		$this->error = true;
	}

	/**
	 * Detects if the form has been submitted
	 * Loads the submitted data into the internal data array if it is a submission, 
	 * stripslashes if necessary and converts any html entities to characters.
	 * Clears the submitted data.
	 * 
	 * @param array $data - data from form, either get or post
	 * @return bool - true on success
	 */
	private function isSubmit(&$data){
		if(isset($data[$this->formSubmitVar])){
			$result=true;
            $this->data = $this->rems($data); // strip slashes if necessary
			// clear the form submit variable from the post or get data, prevents accidental duplicate posts
			unset($data[$this->formSubmitVar]); 
            foreach($this->data as $key => $value){
                // $this->data[$key] = $this->decode($value); // remove any html entities
				if(array_key_exists($key, $this->elements)){
					$this->elements[$key]->field = $this->data[$key]; // map data into form elements
				}
				elseif(array_key_exists($key, $this->hiddenFields)){
					$this->hiddenFieldsData[$key] = $this->data[$key]; // recover any hidden data returned
				}
            }
            // check form submission token if enabled
            if($this->checkToken and trim(self::$tokenChecker) != ""){
                // check token returned is of correct type
                if($this->isError($this->dataCheck($this->data[$this->formSubmitVar], self::$tokenDataType, $this->formSubmitVar))){
                    $this->errorHack = true;
                    $this->errorResult[] = sprintf($this->t($this->tokenDataCheckError), $this->encode($this->data[$this->formSubmitVar]));
                    $this->error = true;
                }
                else{
                    // check for timeout error
                    if(!call_user_func(self::$tokenChecker, $this->data[$this->formSubmitVar], $this->name)){
                        $this->error = true;
                        $this->tokenExpired = true;
                        $this->errorResult[] = $this->t($this->tokenTimeoutError);
                    }
                }
            }
            // preview processing
            if($this->preview){
            	if(isset($this->data[$this->previewBackName])){
            		// preview back button clicked
            		$this->previewBack = true;
            	}
            	if(isset($this->data[$this->previewSaveName])){
            		// preview save button clicked
            		$this->previewSave = true;
            	}
            }
            // process any file objects
            foreach($this->fileObjects as $key => $fileObject){
            	$this->fileObjects[$key]->upload();
            	if($this->fileObjects[$key]->error){
            		$this->addError($this->fileObjects[$key]->errorText);
            	}
				$filePath = $this->fileObjects[$key]->getDisplay($this->data);
				if($filePath and  $this->previewSave){
					$filePath = $this->fileObjects[$key]->move();
				}
            	if($filePath){
            		// put file display path in tDataAdditional so that it can be displayed in preview
            		$this->tDataAdditional[$this->fileObjects[$key]->elName. "Display"] = $filePath;
					$this->addHidden($this->fileObjects[$key]->elName. "Display", $this->fileObjects[$key]->fileName);
					$this->setField($this->fileObjects[$key]->elName, $this->fileObjects[$key]->fileName);
            	}
            }
		}
		else{
			$result=false;
		}
        $this->submitted = $result;
		return($result);
	}

    /**
	 * creates a select query to populate the form.
	 * @return string
	 */
	public function querySelect() {
        /*
        The following data is used:-
            elements - gets everything except submit, rest, button and file.
            alsoAdd - any additional fields needed to be queried.
            whereConditions.
        */

        $query = "select ";

        // for each element in the array parse them if they are not
        // of submit, reset, image, file, button type, take the name from
        // the field if it has been specified, else the element name.
        foreach($this->elements as $element) {
            if(!strpos($this->ignorElements,$element->type) and $element->sql === true) {
                if($element->dbField != "") {
					// gets from specified DB field
                    $querys[] = $this->qt($element->dbField) . " as " .$this->qt($element->name);
                    $this->selectFields[] = $element->dbField;
                }
                else {
					// DB field has same name as form field
                    $querys[] = $this->qt($element->name);
                    $this->selectFields[] = $element->name;
                }
            }
        }
        // add on alsoAdd fields not in form definition
        if(isset($this->alsoAdd)) {
            foreach($this->alsoAdd as $field => $value) {
                $querys[] = $this->qt($field);
                $this->selectFields[] = $element->name;
            }
        }
        $query .= implode(", ",$querys)." from ".$this->dataTable." where " . $this->whereCondition;
        $result = array('query' => $query, 'values' => $this->whereValues);
        $this->resultQuery = $query;
        $this->resultData = $this->whereValues;

        return($result);
    }

    /**
	 * Creates an insert or update query to save data from the form.
	 * @param bool $update - create update query
	 * @return string
	 */
	public function querySave($update=false) {
        /*
        The following data is used:-
            elements - gets everything excepts submit, rest, button and file.
            data - result from form submit
            alsoAdd - any additional fields needed to be saved.
            whereConditions.

            returns an array consisting of the query with ? parameters and an array of data to be used in prepare and execute.
            array(
                "query" => "insert etc.",
                "values" => array(data1, data2, data3, data4)
            )
            also these are put into
                $this->resultQuery;
                $this->resultData;
        */
        // Do a remove shlashes of necessary, metecharacters will be automatically escaped and quoted according to the current DBMS's requirements
        if($update) {
            // do update query

            $query = "update " . $this->dataTable . " set ";
            $values = array();
			$querys = array();
			// for each element parse them if they are not
			// of submit, reset, image, file, button type.
			// if the element exists in the data field add it to the query
			// saving it to the field named, if set, or the element name.
			foreach ($this->elements as $element) {
				if (!strpos($this->ignorElements,$element->type) and $element->sql === true) {
					if($element->dbField != "") {
						$field = $element->dbField;
					}
					else {
						$field = $element->name;
					}
                    $querys[] = $this->qt($field) . " = ?";
                    if(!$this->preview){
						$value = $element->field;
					}
					else{
						if($element->dataType != "gen"){
							$value = $element->field;
						}
						else{
							$value = str_replace("&#034;", '"', $element->field);
						}
					}
					$values[] = $value;
					$this->saveFields[$field] = $value;
				}
			}
			// add on alsoAdd fields
			if(isset($this->alsoAdd)) {
				foreach ($this->alsoAdd as $field => $value) {
					$querys[] = $this->qt($field) . " = ?";
					$this->saveFields[$field] = $value;
                    $values[] = $value;
				}
			}
			// add on set elements and where condition
			$query .= implode(", ",$querys)." where " . $this->whereCondition;
            foreach($this->whereValues as $whereRep){
                $values[] = $whereRep;
            }
        }
        else {
            // do insert query
            $query = "insert into " . $this->dataTable . " (";
            $data = array();
			foreach ($this->elements as $element) {
				if (!strpos($this->ignorElements,$element->type) and $element->sql === true) {
                    // put data into insert query if element is of a data type
                    // has the $this->sql set and has data available or is
                    // of the checkbox type.
					if($element->dbField != ""){
						// database field different name from form field
						$field = $element->dbField;
					}
					else{
						$field = $element->name;
					}
					$fields[] = $this->qt($field);
                    if(!$this->preview){
						$value = $element->field;
					}
					else{
						if($element->dataType != "gen"){
							$value = $element->field;
						}
						else{
							$value = str_replace("&#034;", '"', $element->field);
						}
					}
					$values[] = $value;
					$this->saveFields[$field] = $value;
				}
			}
			// add on alsoAdd fields
			if(isset($this->alsoAdd)) {
				foreach ($this->alsoAdd as $field => $value){
					$fields[] = $this->qt($field);
					$this->saveFields[$field] = $value;
					$values[] = $value;
				}
			}
            $noValues = count($values);
            $valuesRep = str_repeat('?, ', $noValues);
            $valuesRep = substr($valuesRep, 0, -2); // remove last two chars
			$query .= implode(", ",$fields) . ") values (". $valuesRep. ")";
        }
        $this->resultQuery = $query;
        $this->resultData = $values;
        $result = array("query" => $query, "values" => $values);
        return($result);
    }

    // helper routines

	/**
	 * puts the appropriate quotes round filed or a list of fields
	 * and concats them with the supplied string
	 * @param array/string $fields - fields to be quoted
	 * @return string/array
	 */
	public function qt($fields){
		if($this->quote and is_string($fields)){
			$result = $this->quoteStart. $fields. $this->quoteEnd;
		}
		elseif($this->quote){
			foreach($fields as $key => $value){
				$result[$key] = $this->quoteStart. $value. $this->quoteEnd;
			}
		}
		else{
			$result = $fields;
		}

		return($result);
	}

	/**
	 * returns the values destined for the database as an associative array or an object
	 * @param bool $object - if true return an object
	 * @return array/object
	 */
	public function getValues($object = false){
		if($object){
			$values = new \stdClass();
		}
		else{
			$values = array();
		}
		foreach($this->elements as $element) {
			if (!strpos($this->ignorElements,$element->type) and $element->sql === true) {
				if($element->dbField != ""){
					if($object){
						$values->{$element->dbField} = $element->field;
					}
					else{
						$values[$element->dbField] = $element->field;
					}
				}
				else{
					if($object){
						$values->{$element->name} = $element->field;
					}
					else{
						$values[$element->name] = $element->field;
					}
				}
			}
			if(isset($this->alsoAdd)) {
				foreach ($this->alsoAdd as $field => $value){
					if($object){
						$values->{$field} = $value;
					}
					else{
						$values[$field] = $value;
					}
				}
			}
		}
		return($values);
	}
	
	/**
	 * Output the template values as an object
	 * @return \stdClass
	 */
	public function getTemplateValues(){
		$tplData = new \stdClass();
		foreach($this->tData as $key => $field){
			$tplData->$key = $field;
		}
		return $tplData;
	}

    /**
	 * Check a file has been uploaded
	 * @param string $elName
	 * @return $_FILES 
	 */
    public function isFile($elName){
        if(isset($_FILES[$elName]) and is_uploaded_file($_FILES[$elName]['tmp_name'])){
            // named file download
			$this->debugResults[] = "File loaded";
            $upLoadFile =& $_FILES[$elName];
			// find first period in the file name
			$periodPos = strrpos ( $upLoadFile["name"], ".");
			$upLoadFile["extension"] = strtolower(substr(trim($upLoadFile["name"]), $periodPos+1));
			$upLoadFile["nameNe"] = substr($upLoadFile["name"], 0, $periodPos);
        }
        else{
			$this->debugResults[] = "File upload failed";
            $upLoadFile = false;
        }
        return($upLoadFile);
    }

    /**
	 * Check a file type has a valid extension and save to a directory
	 * used with the isFile method.
	 * 
	 * $_FILES['userfile']['name']
	 * The original name of the file on the client machine.
	 * $_FILES['userfile']['type']
	 * The mime type of the file, if the browser provided this information.
	 *  An example would be "image/gif". This mime type is however not checked
	 * on the PHP side and therefore don't take its value for granted.
	 * $_FILES['userfile']['size']
	 * The size, in bytes, of the uploaded file.
	 * $_FILES['userfile']['tmp_name']
	 * The temporary filename of the file in which the uploaded file was stored on the server.
	 * $_FILES['userfile']['error']
	 * The error code associated with this file upload. This element was added in PHP 4.2.0
	 * 
	 * @param array $fileRecord for the field e.g. $_FILES['fieldName']
	 * Added to file record
	 * ['errorDesc'] - Description of error if any
	 * ['extension'] - file extension e.g. pdf, jpg etc.
	 * @param string $directory - path to a directory, end in /
	 * @param string $fileName - file name to be saved without extension
	 * @param array $types - valid types for this extension ("jpg", "png" etc.)
	 * @return bool - false - fail, true - no file upload, file record on success
	 */
    public function upLoadFile(&$fileRecord, $directory, $fileName, array $types){
		$result = true;
 		if(!empty($fileRecord) and $fileRecord['error'] != \UPLOAD_ERR_NO_FILE){
			// check file type
			$uploadFileName = strtolower(trim($fileRecord["name"]));
			$extension = substr($uploadFileName, strrpos($uploadFileName, '.')+1);
			$fileRecord['extension'] = $extension;
			$this->debugResults[] = "File extension $extension";
			if(array_search($extension, $types) !== false){
				$this->debugResults[] = "Valid extension";
				// found the correct file type, check for a download
				if(is_uploaded_file($fileRecord['tmp_name'])){
					if(move_uploaded_file($fileRecord['tmp_name'], $directory. $fileName. ".". $extension)){
						$this->debugResults[] = "file moved to ". $fileName.$extension;
						$fileRecord["savedFileName"] = $fileName. ".". $extension;
						$result = $fileRecord;
					}
					else{
						$fileRecord['errorDesc'] = $fileName. ': '. $this->t('Failed to move file');
					}
				}
				else{
					$result = false;
					switch ($fileRecord['error']) {
						case \UPLOAD_ERR_INI_SIZE:
							$fileRecord['errorDesc'] = $fileName. ': '. $this->t('The uploaded file exceeds the upload_max_filesize directive in php.ini');
							break;
						case \UPLOAD_ERR_FORM_SIZE:
							$fileRecord['errorDesc'] = $fileName. ': '. $this->t('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form');
							break;
						case  \UPLOAD_ERR_PARTIAL:
							$fileRecord['errorDesc'] = $fileName. ': '. $this->t('The uploaded file was only partially uploaded');
							break;
						case \UPLOAD_ERR_NO_FILE:
							$fileRecord['errorDesc'] = $fileName. ': '. $this->t('No file was uploaded');
							break;
						case \UPLOAD_ERR_CANT_WRITE:
							$fileRecord['errorDesc'] = $fileName. ': '. $this->t('Failed to write file to disk');
							break;
						case \UPLOAD_ERR_EXTENSION:
							$fileRecord['errorDesc'] = $fileName. ': '. $this->t('A PHP extension stopped the file upload');
							break;
						default:
							$fileRecord['errorDesc'] = $fileName. ': '. $this->t('Unknown file upload error');
							break;
					}
				}
			}
			else{
				$fileRecord['errorDesc'] = $this->t('Invalid file type ').$fileRecord["extension"];
				$result = false;
			}
		}
        return($result);
    }

	/**
	 * Generate label for an element
	 * @param SfcFe $el - form element
	 * @param string $encapsulate - html of form elelement to be ecapsulated
	 * @return type string - lable, perhaps encapsulating the field
	 */
    private function description($el, $encapsulate="") {
        // returns the description part of the form with any must have characters
        // and assignes the error class if that field input has caused and error.
        $xhtml = '<label';
		if($encapsulate == ""){
			$forAttach = '';
			if($el->id == ""){
				$forAttach = $el->name;
			}
			else{
				$forAttach = $el->id;
			}
			$xhtml .= ' for="'. $forAttach. '"';
		}
		// help text
		if($el->helpText != ""){
			$xhtml .= ' title="'. $this->t($el->helpText). '"';
		}
        // error handling
        if($el->lClass !=""){
        	$xhtml .= ' class="'. $el->lClass;
			if($el->error) {
				$xhtml .= ' '. $this->errorClass;
			}
			$xhtml .= '"';
        }
        elseif($el->error) {
            $xhtml .= ' class="'. $this->errorClass. '"';
        }
        $xhtml .= '>';
		if($encapsulate != "" and !$el->textBefore){
			$xhtml .= $encapsulate;
		}
		// required character
		if(!$el->required){
			$xhtml .= $this->t($el->description);
		}
		else{
			$xhtml .= sprintf($this->t($this->reqChar), $this->t($el->description));
		}
		if($encapsulate != "" and $el->textBefore){
			$xhtml .= $encapsulate;
		}
		$xhtml .= '</label>';
        return($xhtml);
    }

	private function printErrors($errors=false, $localErrors = false){
		// displays the errors produced by the elements
        $xhtml = "";
		if(!$localErrors){
			$errors = $this->errorResult;
		}
		if($this->error and $errors and is_array($errors)){
			$xhtml .= '<ul class="'. $this->errorClass. '">';
			foreach($errors as $error){
				$xhtml .= '<li>'. $this->t($error).'</li>';
			}
			$xhtml .= '</ul>';
		}
        return($xhtml);
	}

    /**
	 * Check data for character errors, e.g. letters in a numeric field and return error objects
	 * @param type $name
	 * @param string $data - data to be checked
	 * @param string $dataType - data type to check against
	 * @param string $description - description of field or data
	 * @param bool $local - generate local error for a form field
	 * @return SfcError - error object or false
	 */
	private function dataCheck($data, $dataType, $description, $local = false){
        /*
    data type, can be:
        text - 0 to 9, a - z, A - Z, \n \r \t . ' " + - _ space
        password - 0 to 9, a - z, A - Z
        dom - 0-9, a-z, A-Z, ._,/ at least one dot, : & etc.
        domchk - checks the domain
        email - <= 128 char, @, 0-9, a-z, A-Z, _.+-
        emailchk - also checks the domain
		date - 0 to 9, /
		time - 0 to 9, :
        phone - 0-9() +-. space
        int - 0-9 -
        real - 0-9 . - e
        hex - 0-9, a-f, A-F
        octal - 0-7
        bin - 0,1
        gen - any character - when re-displayed any special characters are converted to html special entities and then converted back to characters on submission
        */
		if(trim($data) != ""){
			
			$result = $this->checkData->check($dataType, $data);
			
			if($result !== 0){
				// error return
				$errorText = "";
				if(!isset($this->errorTextDataStrings[$result])){
					$errorText = $this->t("No description for error code "). $result;
				}
				elseif($result == 1){
					// character type error return
					if($local){
						$errorText = sprintf($this->t($this->errorTextDataStringsLocal[$result]), $this->checkData->errorMessage);
					}
					else{
						$errorText = sprintf($this->t($this->errorTextData), $this->t($description), sprintf($this->t($this->errorTextDataStrings[$result]), $this->checkData->errorMessage));
					}
				}
				else{
					
					if($local){
						$errorText = $this->t($this->errorTextDataStringsLocal[$result]);
					}
					else{
						$errorText = sprintf($this->t($this->errorTextData), $this->t($description), $this->t($this->errorTextDataStrings[$result]));
					}
				}
				$return = new SfcError($errorText);
			}
			else{
				$return = true;
			}
		}
		else{
			$return = true;
		}
		return($return);
    }

    private function isError($object){
    	// checks for SfcError type

    	$result = false;
    	if(is_object($object) and strcmp("w34u\ssp\sfcerror", strtolower(get_class($object))) == 0){
    		$result = true;
    	}
    	return($result);
    }

    private function isDomain($data, $checkDns = false){
        // Validates the data as a domain
        //
        // parameters
        //  $data - string - data to be verified
        //  $checkDns - bool - check dns entry for domain
        //
        //  returns true on valid data else list of valid characters

        // check length
        if(strlen(trim($data)) > 128){
            return("The url you entered is too long");
        }
        elseif(preg_match("/[^a-zA-Z0-9.\-\/:]/", trim($data))){
            // check it has valid characters
            return("Only the following characters are valid: 0 to 9, a to z, A to Z, ., - \/ :");
        }
        elseif(!preg_match("/\\./", trim($data))){
            // at least on full stop
            return("Your url should have at least one full stop");
        }
        elseif($checkDns && !checkdnsrr($data, "ANY")){
            return("This url does not exist");
        }
        return(true);
    }

    private function isEmail($data, $checkDns = false){
        // Validates the data as email - 0 to 9, a - z, A - Z
        //
        // parameters
        //  $data - string - data to be verified
        //  $checkDns - bool - check dns entry for domain
        //
        //  returns true on valid data else list of valid characters

        // check length
        if(strlen(trim($data)) > 128){
            return("Your email too long");
        }

        if(!preg_match("/^([^@]+)@(.*)$/", trim($data), $parts)){
            return("Your mail needs an @");
        }
        else{
            $user = $parts[1];
            $domain = $parts[2];
        }
        if(preg_match("/[^a-zA-Z0-9.\-_+]/", trim($user))){
            // check it has valid characters
            return("Only the following characters are valid: 0 to 9, a to z, A to Z, .-_+");
        }
        $result = $this->isDomain($domain, $checkDns);
        if($result !== true){
            // invalid domain
            return($result);
        }
        return(true);
    }

	
	function addDataCheck($type, $errorMessage, $validChars, $description=""){
		// add a data check type to the filed data checks array
		
		$this->fieldDataChecks[$type] = new SFC_FieldDataCheck($type, $errorMessage, $validChars, $description);
	}

    private function rems($post){
        // Removes slashes submitted forms etc if magic_quotes_gpc is set
        if(get_magic_quotes_gpc()){
            foreach($post as $keyPost => $valuePost){
                if(!is_array($valuePost)){
                    $post[$keyPost] = stripslashes($valuePost);
                }
                else{
                    $post[$keyPost] = $this->rems($valuePost);
                }
            }
        }
        return($post);
    }

    private function encode($string, $style = ENT_QUOTES, $charset = ""){
        // does a html entities encoding
        if(trim($charset) == ""){
            return(htmlentities($string, $style, $this->charSet));
        }
        else{
            return(htmlentities($string, $style, $charset));
        }
    }

    private function decode($string, $style = ENT_QUOTES, $charset = ""){
        // does a html entities de-coding
        if(trim($charset) == ""){
            return(html_entity_decode($string, $style));
        }
        else{
            return(html_entity_decode($string, $style, $charset));
        }
    }
	
	/**
	 * Configure translation object and method
	 * @param object $translationObject
	 * @param string $translationMethod
	 */
	public static function addTranslation($translationObject, $translationMethod = 't'){
		self::$translate = true;
		self::$tranlator = $translationObject;
		self::$translateMethod = $translationMethod;
	}
	
	/**
	 * Attempt to translate a string of text
	 * @param type $text
	 * @return type
	 */
	private function t($text){
		if(self::$translate and !$this->translateDisable){
			$translateMethod = self::$translateMethod;
			return(self::$tranlator->$translateMethod($text));
		}
		else{
			return($text);
		}
	}
}

class SfcFile{
	// class to upload files to the server
	// One object per file upload field in form->fileObjects array.

	public $elName; // form element name

	public $targetDir; // directory wher the file will be saved
	public $targetView; // path to view an image
	public $fileName; // name of file to be saved
	public $fileNameOld; // name of old file from db or submit with preview
	public $fileNameRoutine=""; // procedure to generate the unique part of the file name
	public $fileNameReplace=""; // totally replace the file name with this, keep extension
	public $validTypes; // array of valid file types
	public $maxSize; // maximum file size allowed

	public $preview = false; // preview upload
	public $previewDir; // directory used for preview
	public $previewView; // path to view image in preview directory
	public $fileInPreview = false; // file is in preview directory

	public $fileUploaded = false; // valid file upload on this submit
	public $fileInfo; // on a file upload file information
	public $fileNameExtension; // extension of file uploaded
	public $fileNameRest; // rest of file name without extension
	public $error = false; // error during file upload
	public $errorText = ""; // error result

	function __construct($el, $targetDir, $targetView, $validTypes, $maxSize, $fileNameRoutine="",$fileNameReplace=""){
		// constructor
		//
		// parameters
		//	$el - file upload form element
		//	$targetDir - string - path to the upload directory
		//	$targetView - string - path to view the uploaded file
		//	$validTypes - array - valid file types for this upload
		//	$maxSize - integer - maximum number of kBytes allowed
		//	$fileNameRoutine - string - eval'd to produce an addition to the file name to guarantee unique
		//	$fileNameReplace - string - totally replace the file name with this, keeps the extension

		if(!is_object($el) or get_class($el) != "w34u\ssp\SfcFe" or $el->type != "file"){
			trigger_error("SfcFile: need a valid form file form element", E_USER_ERROR);
		}

		$this->elName = $el->name;
		$this->desc = $el->description;
		if($targetDir != "" and file_exists($targetDir)){
			$this->targetDir = $targetDir;
		}
		else{
			SSP_error("SfcFile::construct: file upload object, invalid target directory: $targetDir for $this->desc");
		}
		$this->targetView = $targetView;
		if(is_array($validTypes)){
			$this->validTypes = $validTypes;
		}
		else{
			SSP_error("SfcFile::construct: file upload object, needs valid file types array for $this->desc");
		}
		$this->maxSize = $maxSize;
		if($fileNameRoutine != ""){
			$this->fileNameRoutine = $fileNameRoutine;
		}
		if($fileNameReplace != ""){
			$this->fileNameReplace = $fileNameReplace;
		}
	}

	function setPreview($previewDir, $previewView){
		// configure preview function
		//
		// parameters
		//	$previewDir - string - path to preview directory
		//	$previewView - string - path to view preview

		$this->preview = true;
		if($previewDir != "" and file_exists($previewDir)){
			$this->previewDir = $previewDir;
		}
		else{
			SSP_error("SfcFile::construc: file upload object, invalid preview directory: $previewDir for $this->desc");
		}
		$this->previewView = $previewView;
	}

    function upload(){
    	// check for upload of file and move to specified directory

         if(isset($_FILES[$this->elName]) and is_uploaded_file($_FILES[$this->elName]['tmp_name'])){
            // a file has been uploaded
            $this->fileInfo = $_FILES[$this->elName];
            if($this->fileInfo["size"] <= $this->maxSize){
				$periodPos = strrpos($this->fileInfo["name"], ".");
				// get extension
				$this->fileNameExtension = strtolower(substr($this->fileInfo["name"], $periodPos));
				// get rest of file name and remove all spaces and lower case it
				$this->fileNameRest = str_replace(""," ",strtolower(substr($this->fileInfo["name"], 0, $periodPos)));
				if(array_search($this->fileNameExtension, $this->validTypes) !== false){
					// valid file type uploaded
					if($this->fileNameRoutine != ""){
						$this->fileName = $this->fileNameRest. call_user_func($this->fileNameRoutine) . $this->fileNameExtension;
					}
					elseif($this->fileNameReplace != ""){
						$this->fileName = $this->fileNameReplace . $this->fileNameExtension;
					}
					else{
						$this->fileName = $this->fileNameRest . $this->fileNameExtension;
					}
					if(!$this->preview){
						$directory = $this->targetDir;
					}
					else{
						$directory = $this->previewDir;
					}
					if(move_uploaded_file($this->fileInfo["tmp_name"], $directory. $this->fileName)){
						$this->fileUploaded = true;
						if($this->fileNameOld and $this->fileNameOld != $this->fileName and file_exists($directory. $this->fileNameOld)){
							// delete old file if it exists
							unlink($directory. $this->fileNameOld);
						}
					}
					else{
						$this->error("Failed to move file to target directory $directory for $this->desc");
					}
				}
				else{
					$this->error("File uploaded is of invalid type ". $this->fileNameExtension. "  for $this->elName");
				}
			}
			else{
				$this->error("File uploaded is too big ". $this->fileInfo["size"]. " bytes for $this->elName");
			}
        }
    	return($this->fileUploaded);
    }

    function getDisplay($data){
    	// returns a path to display the file using a browser
    	//
    	// parameters
    	//	$data - array - submitted data

    	// file name from form data
    	if(isset($data[$this->elName. "Display"]) and trim($data[$this->elName. "Display"]) != ""){
    		$fileName = $data[$this->elName. "Display"];
    	}
    	elseif(isset($data[$this->elName]) and trim($data[$this->elName]) != ""){
    		$fileName = $data[$this->elName];
    	}
    	else{
    		$fileName = false;
    	}
    	$this->fileNameOld = $fileName;

		if(!$this->preview){
			$directory = $this->targetDir;
			$directoryView = $this->targetView;
		}
		else{
			$directory = $this->previewDir;
			$directoryView = $this->previewView;
		}

    	$file = false;
    	if($this->fileUploaded){
    		// path for just uploaded file
    		$file = $directoryView. $this->fileName;
    	}
    	elseif(!$this->preview and $fileName and $this->isFileName($fileName) and file_exists($directory. $fileName)) {
    		// file name from submitted form data and is valid and exists
    		$file = $directoryView. $fileName;
    		$this->fileName = $fileName;
    	}
    	elseif($this->preview and $fileName){
    		// check if file is in preview or target directories
    		if($this->isFileName($fileName)){
    			if(file_exists($directory. $fileName)){
    				$file = $directoryView. $fileName;
    				$this->fileInPreview = true;
    			}
    			elseif(file_exists($this->targetDir. $fileName)){
    				$file = $this->targetView. $fileName;
    			}
     		$this->fileName = $fileName;
   			}
    	}
    	return($file);
    }

    function move(){
    	// moves file from preview to target directories
    	// returns path to target directory

    	if($this->fileInPreview){
			copy($this->previewDir. $this->fileName, $this->targetDir. $this->fileName);
			unlink($this->previewDir. $this->fileName);
		}
		return($this->targetDir. $this->fileName);
    }

    function error($text){
    	// sets an error for file upload

    	$this->error = true;
    	$this->errorText = $text;
    }

	function isFileName($data){
        // Validates the data as filename - 0 to 9, a - z, A - Z, -_.
        //
        // parameters
        //  $data - string - data to be verified
        //
        //  returns true on valid data else list of valid characters

        // check it has valid characters
        $validChars = "0-9a-zA-z-_.";
        $errorString = "0 to 9, a to z, A to Z and -_.";
        if((strlen($data) > 4) and preg_match("/[". $validChars. "]/", trim($data))){
        	return(true);
        }
        else{
        	return(false);
        }
    }
}

/**
 * Error object returned by error routines
 */
class SfcError{
	/** @var string primary error string */
	public $error = "";
	/** @var string local error used for field errors */
	public $errorLocal = "";

	/**
	 * Constructor
	 * @param type $error - main error
	 * @param type $errorLocal - local error for form fields
	 */
	function __construct($error, $errorLocal = ""){
		$this->error = $error;
		$this->errorLocal = $errorLocal;
	}
}
/* End of file SfcForm.php */
/* Location: ./src/SfcForm.php */