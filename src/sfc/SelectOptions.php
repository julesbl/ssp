<?php

/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	SSP Form
*   Routine:	SelectOptions.php
*   Created:	30-Sep-2016
*   Descrip:	Select options for dropdown.
*
*   Revision:	a
*   Rev. Date	30-Sep-2016
*   Descrip:	Created.
*/
namespace w34u\ssp\sfc;

/**
 * Option object used for select options 
 */
class SelectOptions{
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

/* End of file SelectOptions.php */
/* Location: ./src/sfc/SelectOptions.php */