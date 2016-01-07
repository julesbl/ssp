<?php
/*
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	htmlobjects.php
*   Created:	19/03/2007
*   Descrip:	Objects to help create html structures.
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
*   Rev. Date	19/03/2007
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	23/02/2011
*   Descrip:	Changed to php5 class system.
*/
class classBase{
	// base class to give basic functions to others

	/**
	 * Set the objects properties, erroring if property does not exist
	 * @param string $params - list of comma seperated parmaters
	 */
	public function sv($params){
		// set the objects properties
        if(is_string($params)){
            $parameters = explode(",", $params);
            foreach($parameters as $entry){
                $param = explode("=", $entry);
                $param[0] = trim($param[0]);
                $param[1] = trim($param[1]);
				if($param[1] == "true"){
					$param[1] = true;
				}
				elseif($param[1] == "false"){
					$param[1] = false;
				}
                if(isset($this->$param[0])){
                  $this->$param[0] = $param[1];
				}
                else{
                    die("Form element property $param[0] does not exist in object");
                }
            }
        }
        else{
            die("need a string of data for setting vars");
        }
	}

	/**
	 * Set a property by name
	 * @param string $param - property name
	 * @param string $value
	 */
	public function svp($param, $value){
		if(isset($this->$param)){
		  $this->$param = $value;
		}
		else{
			die("Property $param does not exist in object");
        }
	}
}

/**
 * class for a html link
 */
class htmlLink extends classBase{
	// creates a html link
	/** @var string url for link */
	var $url = ""; // url to document
	/** @var string text to be displayed in link */
	var $text = ""; // text to go in link
	var $id = ""; // id of link
	var $lClass = ""; // class for link
	var $target = ""; // target, frame or window
	var $title = ""; // link title
	var $misc = ""; // additional parameters eg javascript

	function __construct($url, $text){
		// constructor
		$this->url = $url;
		$this->text = $text;
	}

	function cLink(){
		// creates xhml for link
		$xhtml = '<a href="'. $this->url. '"';
		if($this->id != ""){
			$xhtml .= ' id="'. $this->id. '"';
		}
		if($this->lClass != ""){
			$xhtml .= ' class="'. $this->lClass. '"';
		}
		if($this->target != ""){
			$xhtml .= ' target="'. $this->target. '"';
		}
		if($this->title != ""){
			$xhtml .= ' title="'. $this->title. '"';
		}
		else{
			$xhtml .= ' title="'. $this->text. '"';
		}
		if($this->misc != ""){
			$xhtml .= ' '. $this->misc;
		}
		$xhtml .= '>'. $this->text. '</a>';
		return($xhtml);
	}
}

class menuEntry extends htmlLink{
	// defines a menu entry

	var $entryId = "";
	var $entryClass = "";
	var $entryHighlight = ""; // added to class on construction
	var $entryStyle = "";

	function __construct($url, $text){
		// constructor
		$this->url = $url;
		$this->text = $text;
	}

	function cEntry(){
		// generates html for menu item
		$xhtml = '<li';
		if($this->entryId != ""){
			$xhtml .= ' id="'. $this->entryId. '"';
		}
		if($this->entryHighlight != ""){
			$xhtml .= ' class="'. $this->entryHighlight;
			if($this->entryClass != ""){
				$xhtml .= ' '. $this->entryClass;
			}
			$xhtml .= '"';
		}
		elseif($this->entryClass != ""){
			$xhtml .= ' class="'. $this->entryClass. '"';
		}
		if($this->entryStyle != ""){
			$xhtml .= ' style="'. $this->entryStyle. '"';
		}
		if($this->url != ""){
			$xhtml .= '>'. $this->cLink(). '</li>';
		}
		elseif($this->text != ""){
			$xhtml .= '>'. $this->text. '</li>';
		}
		else{
			$xhtml .= '>&nbsp;</li>';
		}
		return($xhtml);
	}
}

class menuGen{
	// generates menus using unordered lists

	var $highlightClass = "here"; // class used to hightlight menu entry
	var $menu = ""; // array of menu elements
	var $currentEntry = 0; // current menu entry
	var $id = ""; // id for menu
	var $mclass = ""; // class for menu

	function __construct($highlightClass = ""){
		// generates the menu based on information in an array

		// parameters
		//	$highlightClass - string - class used to highlight current entry

		if($highlightClass != ""){
			$this->highLightClass = $highlightClass;
		}
	}

	function add($url, $text, $highLight=false){
		// adds a menu entry

		$this->currentEntry++;
		$this->menu[$this->currentEntry] = new menuEntry($url, $text);
		if($highLight){
			$this->menu[$this->currentEntry]->entryHighlight = $this->highlightClass;
		}
	}

	function sv($params){
		// set properties for menu entry

		$this->menu[$this->currentEntry]->sv($params);
	}

	function svp($param, $value){
		// set properties for menu entry

		$this->menu[$this->currentEntry]->svp($param, $value);
	}

	function cMenu(){
		// create menu xhtml

		$xhtml = '<ul';
		if($this->id != ""){
			$xhtml .= ' id="'. $this->id. '"';
		}
		if($this->mclass != ""){
			$xhtml .= ' class="'. $this->mclass. '"';
		}
		$xhtml .= '>';
		foreach($this->menu as $entry){
			$xhtml .= $entry->cEntry();
		}
		$xhtml .= '</ul>';
		return($xhtml);
	}
}

function formDropdown($name, $data, $current="", $misc="", $id=""){
	// return a dropdown selector for a form

	if($misc != ""){
		$miscAdd = " ". $misc;
	}
	else{
		$miscAdd = "";
	}
	if($id!=""){
		$idParam = $id;
	}
	else{
		$idParam = $name;
	}
	$html = '<select name="'. $name. '" id="'. $idParam. '"'. $miscAdd. '>';
	if(is_string($current)){
		$stringType = true;
	}
	else{
		$stringType = false;
	}
	foreach($data as $key => $value){
		if(($stringType and strcmp($current, $key)==0) or (!$stringType and $current == $key)){
			$selected = ' selected = "selected"';
		}
		else{
			$selected = "";
		}
		$html .= '<option value="'. $key. '" '. $selected. '>'. $value. '</option>';
	}
	$html .= '</select>';
	return($html);
}
/* End of file htmlobjects.php */
/* Location: ./sspincludes/htmlobjects.php */