<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site protection
*   Routine:	ListerSave.php
*   Created:	18/01/2016
*   Descrip:	List postion saving class, moved from Lister.php file
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
*   Rev. Date	07/02/2005
*   Descrip:	Created.
*/

namespace w34u\ssp;

 class ListerSave{
    var $page = 1; // position in list
    var $limit = 0; // number of rows per page
	/** @var int number of rows paged last time */
	var $lastNumber = 0;
	/** @var string list id to prevent tow list on the same page interacting */
	var $listId = "";

    /**
	 * constructor
	 *
	 * parameters
	 * 	@limit - int - number of lines per page
	 */
	function __construct($limit, $listId=""){
        // Constructor
        $this->limit = $limit;
		$this->listId = $listId;
    }

	/** updates the lister save from gets and posts
	*/
	function update(){

        // Number of lines to be printed
		SSP_changeParam($this->limit, "limit". $this->listId, false, "int");
        // Page list starting position change
		SSP_changeParam($this->page, "page". $this->listId, true, "int");
	}

	/**
	 * reset the page to one
	 */
	function res(){
		// 
		$this->page = 1;
	}

}

/* End of file ListerSave.php */
/* Location: ./src/ListerSave.php */