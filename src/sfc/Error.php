<?php

/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	SSP Form
*   Routine:	Error.php
*   Created:	30-Sep-2016
*   Descrip:	Error class.
*
*   Revision:	a
*   Rev. Date	30-Sep-2016
*   Descrip:	Created.
*/
namespace w34u\ssp\sfc;

/**
 * Error object returned by data checking routines 
 */
class Error{
	/** @var string primary error string */
	public $error = "";
	/** @var string local error used for field errors */
	public $errorLocal = "";

	/**
	 * Constructor
	 * @param type $error - main error
	 * @param type $errorLocal - local error for form fields
	 */
	public function __construct($error, $errorLocal = ""){
		$this->error = $error;
		$this->errorLocal = $errorLocal;
	}
}
/* End of file Error.php */
/* Location:  */