<?php
/*
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	htmlobjects.php
*   Created:	19/03/2007
*   Descrip:	Objects to help create html structures.
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
*   Rev. Date	19/03/2007
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	23/02/2011
*   Descrip:	Changed to php5 class system.
*
*   Revision:	c
*   Rev. Date	14/01/2016
*   Descrip:	Composer implemented.
*/

namespace w34u\ssp;

/**
 * Base class for adding parameters to html objects
 */
class ClassBase{
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
class HtmlLink extends ClassBase{
	// creates a html link
	/** 
	 * url for link
	 * @var string */
	public $url = "";
	/** 
	 * text to be displayed in link
	 * @var string */
	public $text = "";
	/**
	 * id of link
	 * @var string
	 */
	public $id = "";
	/**
	 * class for link
	 * @var string
	 */
	public $lClass = "";
	/**
	 * target, frame or window
	 * @var string
	 */
	public $target = "";
	/**
	 * link title
	 * @var string 
	 */
	public $title = "";
	/**
	 * additional parameters eg javascript
	 * @var string
	 */
	public $misc = "";

	/**
	 * Constructor
	 * @param string $url - url for link
	 * @param string $text - text for link
	 */
	public function __construct($url, $text){
		// constructor
		$this->url = $url;
		$this->text = $text;
	}

	/**
	 * Create the xhtml for hte link
	 * @return string - xhtml
	 */
	public function cLink(){
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

/**
 * Class for a menu entry
 */
class MenuEntry extends HtmlLink{
	/**
	 * Entry ID
	 * @var string
	 */
	public $entryId = "";
	/**
	 * Entry class
	 * @var string
	 */
	public $entryClass = "";
	/**
	 * Class added to the class list on construction if entry selected
	 * @var string
	 */
	public $entryHighlight = ""; // added to class on construction
	/**
	 * Styling added to the entry
	 * @var string
	 */
	public $entryStyle = "";
	
	public function cEntry(){
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

/**
 * Menu generator class
 */
class MenuGen{
	/**
	 * Class used to highlight selected menu entries
	 * @var string
	 */
	public $highlightClass = "here";
	/**
	 * array of menu elements
	 * @var array
	 */
	public $menu = [];
	/**
	 * current menu entry
	 * @var int 
	 */
	public $currentEntry = 0;
	/**
	 * id for menu
	 * @var string
	 */
	public $id = "";
	/**
	 * class for menu
	 * @var string
	 */
	public $mclass = "";
	/**
	 * Current menu entry
	 * @var w34u\ssp\MenuEntry
	 */
	public $currentItem = null;
	
	/**
	 * Constructor
	 * @param string $highlightClass - class for heighlighting selected menu entry
	 */
	public function __construct($highlightClass = ""){
		// generates the menu based on information in an array

		// parameters
		//	$highlightClass - string - class used to highlight current entry

		if($highlightClass != ""){
			$this->highLightClass = $highlightClass;
		}
	}

	/**
	 * adds a menu entry
	 * @param string $url - url for menu entry
	 * @param type $text - text for menu entry
	 * @param type $highLight - set to true if current entry
	 */
	public function add($url, $text, $highLight=false){
		$this->currentEntry++;
		$this->currentItem = $this->menu[$this->currentEntry] = new MenuEntry($url, $text);
		if($highLight){
			$this->menu[$this->currentEntry]->entryHighlight = $this->highlightClass;
		}
	}
	
	/**
	 * Set properties of a menu entry
	 * @param string $params - parameters
	 */
	public function sv($params){
		$this->menu[$this->currentEntry]->sv($params);
	}

	/**
	 * set properties for menu entry
	 * @param string $param - parameter name
	 * @param misc $value - value to be set
	 */
	public function svp($param, $value){
		$this->menu[$this->currentEntry]->svp($param, $value);
	}

	/**
	 * create menu xhtml
	 * @return string - xhtml for the menu
	 */
	public function cMenu(){
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

/**
 * Create a form dropdown
 * @param string $name - name of the form
 * @param array $data - dropdown entries
 * @param misc $current - current selected value
 * @param string $misc - addtional stuff for select tag
 * @param string $id - id for the form
 * @return string - return html
 */
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
/* End of file MenuGen.php */
/* Location: ./src/MenuGen.php */