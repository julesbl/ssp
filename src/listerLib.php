<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site protection
*   Routine:	listerLib.php
*   Created:	07/02/2005
*   Descrip:	Class for creating a listing form.
*
*   Copyright 2005-2009 Julian Blundell, w34u
*
*   This file is part of Simple Site Protection (SSP).
*
*   SSP is free software; you can redistribute it and/or modify
*   it under the terms of the COMMON DEVELOPMENT AND DISTRIBUTION
*   LICENSE (CDDL) Version 1.0 as published by the Open Source Initiative.
*
*   SSP is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   COMMON DEVELOPMENT AND DISTRIBUTION LICENSE (CDDL) for more details.
*
*   You should have received a copy of the COMMON DEVELOPMENT AND DISTRIBUTION
*   LICENSE (CDDL) along with SSP; if not, view at
*   http://www.opensource.org; http://www.opensource.org/licenses/cddl1.php
*
*   Revision:	a
*   Rev. Date	07/02/2005
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	30/11/2006
*   Descrip:	memberFilter and lister streamlined using templates and SFP forms.
*
*   Revision:	c
*   Rev. Date	29/04/2009
*   Descrip:	Alternate lines added.
*
*   Revision:	d
*   Rev. Date	23/02/2011
*   Descrip:	Changed to php5 class system.
*
*/

/**
 * List postion saving class
 *
 * properties
 * 	@page - int - position in list
 * 	@limit - int - number of rowsa per page
 */
class listerSave{
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

/**
 * Display a list of items
 *
 * properties
 *   @listerSave; // object containing saved information
 *   @db - SSP_SB - database object
 *   @path - string - url path to script
 *   @noResult - bool - display no result text
 *   @numRows - int - number of rows returned by the query
 *   @nextPage - bool - internal display next page link
 *   @previousPage - bool - display previous page link
 *   @pages - int - number of pages
 *   @pageStart - int - record at which the page is to start
 *   @prevPageChar - "&lt; " - previous page text
 *   @nextPageChar - " &gt;" - next page text
 *   @pageDisplay - int - maximum number of pages to display in navigation
 *   @pageDisplayStart - int - line start of page display for previous pages
 *   @pageDisplayEnd - int - line end of page display for next pages
 *   @prevPagesChar - "&#171;" - previous pages text
 *   @nextPagesChar - "&#187;" - next pages text
 *   @previousPages - bool - internal display previous pages link
 *   @nextPages - bool - internal display next pages link
 *   @par - string - addtional get parameters for page navigation
 *   @processLine - string - routine to process each line
*/
class lister{
    // class to display a list of items
    var $listerSave; // object containing saved information
    var $db; // database object
	var $path; // path to script
	/** @var string id to prevent two list on the same page interacting */
	var $listId = "";
    var $noResult = true; // display no result text
    var $numRows = 0; // number of rows returned by the query
    var $nextPage = false; // display next page link
    var $previousPage = false; // display previous page link
    var $pages = 0; // number of pages
	var $pageStart = 0; // record at which the page is to start
	var $prevPageChar="&lt;";
	var $nextPageChar=" &gt;";
	var $pageDisplay = 10; // maximum number of pages to display in navigation
	var $pageDisplayStart = 1; // start of page display
	var $pageDisplayEnd = 10; // end of page display
	var $prevPagesChar = "&#171;";
	var $nextPagesChar = "&#187;";
	var $previousPages = false; // display previous pages link
	var $nextPages = false; // display next pages link
	var $par = ""; // addtional get parameters
	var $processLine = ""; // routine to process a line
	var $processLineObject = false;

	/** @var object first record produced on he page */
	var $pageFirstRecord = false;
	/** @var object last record produced on he page */
	var $pageLastRecord = false;

	/** constructor
	*
	* parameters
	*	@listerSave - object - lister save type, keeps track of page etc.
	*	@result - SSP_DB - database object
	*	@path - string - path to script using the lister for the page nav
	*	@numRows - integer - number of rows produced by an external application
	*	@par - string - additional get parameters for the page nav
	*/
    function __construct(&$listerSave, $db, $path, $numRows=0, $par="", $listId=""){

        $this->listerSave =& $listerSave; // save object for lister parameters
        $this->db = $db; // database object
		$this->path = $path;
		$this->par = $par;
		$this->listId = $listId;

		if($numRows==0){
			$this->numRows = $this->db->numRows();
		}
		else{
			$this->numRows = $numRows;
		}

		// reset current page if number of rows has changed by more than a page
		if($this->numRows < $this->listerSave->lastNumber - $this->listerSave->limit + 1){
			$this->listerSave->page = 1;
		}
		$this->listerSave->lastNumber = $this->numRows;

		if($this->numRows == 0){
			$this->noResult = true;
		}
		else{
			$this->noResult = false;
			$this->pages = ceil($this->numRows/$this->listerSave->limit);
			$numberOfDisplayPages = ceil($this->pages/$this->pageDisplay);
			$pageDisplayCurrent = floor(($this->listerSave->page-1)/$this->pageDisplay) +1;
			$this->pageDisplayStart = ($pageDisplayCurrent-1) * $this->pageDisplay + 1;
			$this->pageDisplayEnd = $pageDisplayCurrent * $this->pageDisplay;
			if($this->listerSave->page > $this->pages){
				$this->listerSave->page = 0;
			}
			if($pageDisplayCurrent > 1){
				$this->previousPages = true;
			}
			if($this->listerSave->page > 1){
				$this->previousPage = true;
			}
			if($this->listerSave->page < $this->pages){
				$this->nextPage = true;
			}
			if($pageDisplayCurrent < $numberOfDisplayPages){
				$this->nextPages = true;
			}
			$this->pageStart = ($this->listerSave->page -1) * $this->listerSave->limit;
		}
    }
	
	/**
	 * Set up a function to process the line
	 * @param string $functionName - name of function to process the line
	 * @param object $functionObject - object to which the function belongs
	 */
	public function setLineFunction($functionName, $functionObject = false){
		$this->processLine = $functionName;
		$this->processLineObject = $functionObject;
	}

	/** creates a page navigation element
	*
	* parameters
	*	@selClass - string - page selection highlight class
	*	@pageNavClass - string - page navigation class
	*/
    function pageNav($pageNavClass="pageNav", $selClass="here"){

        $nav = "";
        if(!$this->noResult and ($this->pages > 1)){
            $nav .= '<ul class="'. $pageNavClass. '">';

            if($this->previousPages){
                $pos = $this->pageDisplayStart - 1;
                $nav .= '<li><a href="'.$this->path . '?page'. $this->listId. '='. $pos. $this->par. '">'. $this->prevPagesChar. '</a></li>';
            }

            if($this->previousPage){
                $pos = $this->listerSave->page - 1;
                $nav .= '<li><a href="'.$this->path . '?page'. $this->listId. '='. $pos. $this->par. '">'. $this->prevPageChar. '</a></li>';
            }
            for($page = $this->pageDisplayStart ; $page <= $this->pageDisplayEnd and $page <= $this->pages; $page++){
                if($page == $this->listerSave->page){
                    $selection = ' class="'. $selClass. '"';
                }
                else{
                    $selection = '';
                }
                $nav .= '<li'. $selection. '><a href="'. $this->path. '?page'. $this->listId. '='. $page. $this->par. '">'. $page. '</a></li>';
			}

            if($this->nextPage){
                $pos = $this->listerSave->page + 1;
                $nav .= '<li><a href="'.$this->path . '?page'. $this->listId. '='. $pos. $this->par. '">'. $this->nextPageChar. '</a></li>';
            }

            if($this->nextPages){
                $pos = $this->pageDisplayEnd + 1;
                $nav .= '<li><a href="'.$this->path . '?page'. $this->listId. '='. $pos. $this->par. '">'. $this->nextPagesChar. '</a></li>';
            }

            $nav .= '</ul>';
        }
        return($nav);
    }

	/** displays the result from the queryList
	*
	* Parameters
	*	@content - array - additional content, paths etc to use with lister
	*	@listEvenLineTpl - string - file name for template used to format even line of results
	*	@noResultsTpl - string - template used for no results
	*	@listOddLineTpl - string - file name for template used to format odd line of results
	*	@overWrite - bool - content must over write the current, not be merged
	*/
    function displayList($content, $listEvenLineTpl, $noResultsTpl, $listOddLineTpl="", $overWrite=false){

        $listing = "";
		if($this->noResult){
			$list = new Template($content, $noResultsTpl, false);
			$listing = $list->output();
		}
		else{
			$listEven = new Template($content, $listEvenLineTpl, false);
			if($listOddLineTpl != ""){
				$listOdd = new Template($content, $listOddLineTpl, false);
			}
			else{
				$listOdd = new Template($content, $listEvenLineTpl, false);
			}
			$line = $this->pageStart;
			$i = 1;
			$evenLine = true;
			$this->db->move($line);
			$firstLine = true;
			while($row = $this->db->fetchRow(true) and $i <= $this->listerSave->limit){
				if(is_array($content)){
					$rowContent = array_merge($row, $content);
				}
				else{
					$rowContent = $row;
				}
				if($firstLine){
					$rowContent["SSP_firstLine"] = true;
				}
				else{
					$rowContent["SSP_firstLine"] = false;
				}
				$displayLine = true;
				if($this->processLine != ""){
					if($this->processLineObject === false){
						$rowContent = call_user_func($this->processLine, $rowContent);
					}
					else{
						$rowContent = call_user_func([$this->processLineObject, $this->processLine], $rowContent);
					}
					if(is_bool($rowContent) and !$rowContent){
						$displayLine = false;
					}
					elseif(!is_array($rowContent)){
						trigger_error("Lister Lib: invalid result from $this->processLine", E_USER_ERROR);
					}
				}
				elseif(!is_array($rowContent)){
					trigger_error("Lister Lib: invalid row content", E_USER_ERROR);
				}
				if($displayLine){
					if($evenLine){
						$listEven->restart($rowContent, $overWrite);
						$listing .= $listEven->output();
					}
					else{
						$listOdd->restart($rowContent, $overWrite);
						$listing .= $listOdd->output();
					}
					if($evenLine){
						$evenLine = false;
					}
					else{
						$evenLine  = true;
					}
				}
				$i++;
				if($firstLine){
					$this->pageFirstRecord = $rowContent;
					$firstLine = false;
				}
			}
		}
		if(!$this->noResult){
			$this->pageLastRecord = $rowContent;
		}
        return($listing);
    }
}
/* End of file listerLib.php */
/* Location: ./sspincludes/listerLib.php */