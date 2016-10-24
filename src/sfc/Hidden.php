<?php

/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	SSP Form
*   Routine:	Hidden.php
*   Created:	30-Sep-2016
*   Descrip:	Element for hidden fields added to the form as the sfc\Form::hiddenFields array
*
*   Revision:	a
*   Rev. Date	30-Sep-2016
*   Descrip:	Created.
*/
namespace w34u\ssp\sfc;

class Hidden{
	/**
	 * name of field
	 * @var string
	 */
    public $name;
	/**
	 * data to be stored in hidden field
	 * @var string 
	 */
    public $data;
	/**
	 * type of data to be stored, see SFC_FE for more information
	 * @var string
	 */
    public $dataType = "text";
	/**
	 * class assigned to the hidden element
	 * @var type 
	 */
    public $elClass = "";

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
/* End of file Hidden.php */
/* Location: ./src/sfc/Hidden.php */