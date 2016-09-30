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
*   Descrip:	Class for hidden fields.
*
*   Revision:	a
*   Rev. Date	30-Sep-2016
*   Descrip:	Created.
*/
namespace w34u\ssp\sfc;

class Hidden{
    // Element for hidden fields added to the form as the sfc\Form::hiddenFields array
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
/* End of file Hidden.php */
/* Location: ./src/sfc/Hidden.php */