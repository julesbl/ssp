<?php

/**
 *   Site by w34u
 *   http://www.w34u.com
 *   info@w34u.com
 *   +44 (0)7833 512221
 *
 *   Project:	SSP - Formclasses
 *   Routine:	Fe.php
 *   Created:	30-Sep-2016
 *   Descrip:	Form element class.
 *
 *   Revision:	a
 *   Rev. Date	30-Sep-2016
 *   Descrip:	Created.
 */

namespace w34u\ssp\sfc;

/**
 * Class to define the form elements
 */
class Fe {

	// html properties
	/** type of form element - text, password, select, textarea, startrow, endrow, colrow, submit, reset, button, image, file, check, radio
	 * @var string */
	public $type = "text";

	/**
	 *  name of form field, also its id and database field
	 * @var string */
	public $name = "";

	/** 
	 * id for form element if different from the element name
	 * @var string  */
	public $id = "";

	/** 
	 * description for field, used in form and display, is also text for submit, reset and button.
	 * @var string */
	public $description = "";

	/** 
	 * Placeholder text, if set to true uses description
	 * @var bool|string  */
	public $placeholder = false;

	/** 
	 * addtional class to be applid to the element
	 * @var string  */
	public $elClass = "";

	/** 
	 * addtional class to be applied to the label
	 * @var string  */
	public $lClass = "";

	/** 
	 * default type for the <button tag
	 * @var string  */
	public $buttonType = 'submit';

	/** 
	 * maximum amount of characters that can be entered, html limiter
	 * @var int  */
	public $maxLength = "";

	/** 
	 * data for dropdowns, checkboxes etc.
	 * @var array  */
	public $data = "";

	/** 
	 * valid results for a selection or radio button, particularly useful for ajax loading of the element
	 * @var array  */
	public $validResults = array();
	/**
	 * Don't check for the valid result from a selection
	 * @var bool
	 */
	public $dont_check_valid_results = false;

	/** 
	 * default value for elements.
	 * @var string  */
	public $deflt = "";
	/**
	 *  give the element and access key
	 * @var string
	 */
	public $accessKey = "";
	/**
	 * relative path for image
	 * @var string
	 */
	public $image = "";
	/**
	 * radio, vertical or horizontal
	 * @var string 
	 */
	public $orientation = "horizontal";
	/**
	 * show the radio buttons text;
	 * @var bool
	 */
	public $radioDesc = true;
	/**
	 * text before radio buttons and encapsulated elements
	 * @var bool 
	 */
	public $textBefore = true;
	/**
	 * put the form element in the description label
	 * @var bool
	 */
	public $encap = false;
	/**
	 * event, javascript and other stuff for the tag
	 * @var string
	 */
	public $script = "";
	/**
	 * look for external data in elementSelections for dropdown etc.
	 * @var bool 
	 */
	public $useExternal = false;
	/**
	 * tabindex for field
	 * @var string
	 */
	public $tabIndex = "";
	/**
	 * if set to true the text or text area field is readonly, user cannot modify field
	 * @var bool
	 */
	public $readonly = false;
	/**
	 * if set to true text or textarea is disabled on first display
	 * @var bool 
	 */
	public $disabled = false;
	/**
	 * help text for filling in the form used in title and used in third column if enabled
	 * @var string
	 */
	public $helpText = "";
	/**
	 * general text to be put in box with input element, only for generated page
	 * @var string 
	 */
	public $genText = "";
	/**
	 * locally applied style
	 * @var string 
	 */
	public $style = "";
	/**
	 * language direction, can be ltr or rtl
	 * @var string 
	 */
	public $ldir = "";
	/**
	 * language code for element
	 * @var string
	 */
	public $lang = "";
	/**
	 * width in characters for input field
	 * @var string
	 */
	public $width = "";
	/**
	 * number of lines in textarea, dropdown, select
	 * @var string
	 */
	public $lines = "";
	/** 
	 * accept field for the file field
	 * @var string */
	public $accept = "";
	// sql properties
	/**
	 * database field name if different from element name.
	 * @var string 
	 */
	public $dbField = "";
	/**
	 * If false the field is never loaded from input data
	 * @var bool
	 */
	public $load = true;
	/**
	 * If false sql is never generated for this field
	 * @var bool 
	 */
	public $sql = true;
	
	// error handling properties
	/**
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
	 * @var string 
	 */
	public $dataType = "text"; // data type
	/**
	 * data for this element returned from form submission or sql load
	 * @var type 
	 */
	public $field = "";
	/**
	 * field is required
	 * @var bool
	 */
	public $required = false;
	/**
	 * Aria described by id to add enhanced description to an element
	 * @var string
	 */
	public $ariaDescribedby = null;
	/**
	 * Auto complete property
	 * @var string
	 */
	public $autocomplete = null;
	/**
	 * maximum number characters allowed, throws error if greater, 0 means no test unless in the data type.
	 * @var int
	 */
	public $maxChar = '0';
	/**
	 * minimum number of chars, less than throws error
	 * @var int 
	 */
	public $minChar = '0';
	/**
	 * if given a number will round a real number to this
	 * @var bool
	 */
	public $precision = false;
	/**
	 * Can be set by $this->check() or external code
	 * @var bool
	 */
	public $error = false;
	/** 
	 * Error strings to be displayed locally
	 * @var array  */
	public $errorText = array();
	/**
	 * Check value entered using user supplied function
	 * @var bool
	 */
	public $check = false;
	/**
	 * used by call_user_func, function($elements, $more) elements and additional data passed by reference, do not put brackets on function name
	 * @var string
	 */
	public $errorFunction = '';

	/**
	 * Constructo for a form element
	 * @global int $SFC_Page page variable, not used at the moment
	 * @param string $type - type of element eg. text, select
	 * @param string $name - name of the element, used for id as well
	 * @param string $description - description used in label and in alt unless help is specified
	 * @param array $data - data for selects, radio etc.
	 */

	public function __construct($type, $name, $description = "", $data = "") {
		$this->type = $type;
		$this->name = $name;
		$this->description = $description;
		if (is_array($data)) {
			$this->data = $data;
		} elseif ($data != "") {
			$this->deflt = $data;
		}
	}

	/**
	 * Basic element data checking
	 * @param array $errorStrings - array of strings to return error
	 * @return bool - true on error
	 */
	public function checkField($errorStrings) {
		if ($this->required and trim($this->field) == "") {
			// checks for an empty required field
			$this->addError(sprintf($errorStrings["required"], $this->description));
		}
		if ($this->maxChar > 0 and ( strlen($this->field) > $this->maxChar)) {
			$this->addError(sprintf($errorStrings["maxChar"], $this->description, $this->maxChar));
		}
		if ($this->minChar > 0 and ( strlen($this->field) < $this->minChar)) {
			$this->addError(sprintf($errorStrings["minChar"], $this->description, $this->minChar));
		}
		if ($this->check and call_user_func($this->errorFunction, $this->field)) {
			$this->addError($this->errorFunctionText);
		}
		if ($this->type == "radio" and trim($this->field) == "") {
			// checks for an empty radio button field
			$this->addError(sprintf($errorStrings["required"], $this->description));
		}
		if ($this->dataType == "real" and $this->precision) {
			// rounds field to correct precision
			$this->field = round($this->field, $this->precision);
		}
		if ($this->dont_check_valid_results === false and ($this->type == 'select' or $this->type == 'radio') and trim($this->field) !== "") {
			// get valid results if not already supplied
			if (count($this->validResults) == 0) {
				$this->getValidResults($this->data);
			}
			if (array_search(trim($this->field), $this->validResults) === false) {
				$this->addError(sprintf($errorStrings["errorVal"], $this->name));
			}
		}
		return($this->error);
	}

	/**
	 * Gets the valid results from the field data
	 * @param array $data - field data
	 */
	private function getValidResults($data) {
		foreach ($data as $key => $options) {
			if (is_object($options) and isset($options->type) and $options->type == "optgroup") {
				$this->getValidResults($options->options);
			} else {
				$this->validResults[] = $key;
			}
		}
	}

	/**
	 * Add and error ro the field
	 * @param string $errorText - error to be added to the field
	 */
	public function addError($errorText) {
		// adds an error to the list of errors, usually triggers the error flag
		$this->errorText[] = $errorText;
		$this->error = true;
	}

}

/* End of file fe.php */
/* Location: ./src/sfc/fe.php */