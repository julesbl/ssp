<?php

/*
 *   Site by w34u
 *   http://www.w34u.com
 *   info@w34u.com
 *   +44 (0)7833 512221
 *
 *   Project:	Simple Site Protection
 *   Routine:	Template.php
 *   Created:	23/09/2005
 *   Descrip:	Classes to implement basic templating functions for the SSP applications.
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
 *   Rev. Date	23/09/2005
 *   Descrip:	Created, moved from adminclasses.php.
 *
 *   Revision:	b
 *   Rev. Date	19/03/2006
 *   Descrip:	Conditional template area display code added.
 *
 *   Revision:	c
 *   Rev. Date	18/08/2006
 *   Descrip:	Output automatically converted to html entites unless otherwise specified.
 *   Helps prevent JavaScript injection attacks.
 *
 *   Revision:	d
 *   Rev. Date	23/02/2011
 *   Descrip:	Changed to php5 class system.
 *
 *   Revision:	e
 *   Rev. Date	13/01/2016
 *   Descrip:	Changed to psr-4.
 */

namespace w34u\ssp;

class Template {

	/**
	 * name of the template
	 * @var string
	 */
	private $templateName = "";

	/**
	 * path to template
	 * @var string
	 */
	private $templatePath = "";

	/**
	 * template loaded into this array
	 * @var array
	 */
	private $templateArray = array();

	/**
	 * number of lines in template
	 * @var int
	 */
	private $arrayLength = 0;

	/**
	 * Current line being processed
	 * @var int 
	 */
	private $contentPosition = 0;

	/**
	 * array of strings to be replaced within the file
	 * @var type 
	 */
	private $replaces = array();

	/**
	 * functions to be applied to particular field, if "" no htmlentites
	 * @var type 
	 */
	public $repFunctions = array();

	/**
	 * automatically does htmlentities on replacement strings
	 * @var boolean
	 */
	public $encode = true;

	/**
	 * output to stdout if true
	 * @var boolean
	 */
	public $display = true;

	/**
	 * contains resulting page
	 * @var string
	 */
	public $output = "";

	/**
	 * replace the index tag
	 * @var boolean
	 */
	public $replaceIndex = false;

	/**
	 * index tag to replace {0} in any tag.
	 * @var int
	 */
	public $indexNo = "0";

	/**
	 * number of lines to be returned before output begins
	 * @var int
	 */
	public $numberReturnLines = 0;

	/**
	 * lines returned before output
	 * @var array
	 */
	public $returnedLines = array();

	/**
	 * character set for mutibyte string functions and htmlentities for this template
	 * @var string
	 */
	private $charsetHtml = '';
	
	/**
	 * character set for mutibyte string functions and htmlentities for this template
	 * @var string
	 */
	private static $charsetHtmlStatic = "UTF-8";

	/**
	 * type of html encoding as used by htmlentities
	 * @var int
	 */
	private $charsetQuote = 0;
	
	/**
	 * type of html encoding as used by htmlentities
	 * @var int
	 */
	private static $charsetQuoteStatic = \ENT_QUOTES;
	
	/**
	 * Exit template processing loop
	 * @var boolean
	 */
	private $exitLoop = false;
	
	/**
	 * Dont do output of this line and maybe others
	 * @var boolean
	 */
	private $skipOutput = false;
	
	/**
	 * Postion of process in a line
	 * @var int
	 */
	private $valuePos = 0;




	var $cache = false; // always cache this template
	var $cacheEnabled = false; // cacheing is enabled
	var $cachDir = ""; // cache directory

	/** @var array of string - set of paths in which to find templates */
	private static $paths = array(
		'./',
		'templates/'
	);

	/**
	 * Constructor
	 *
	 * @param string/array $replaces - either the title or an array of elements to be replaced
	 * @param string/array $templateFile - template file to be used or array of text to be used as a template
	 * @param boolean $display - sends directly to STDOUT if true
	 * @param string $charsetHtml - character set for mutibyte string for this template
	 * @param boolean $charsetQuotes - type of html encoding as used by htmlentities
	 */
	function __construct($replaces, $templateFile, $display = false, $charsetHtml = false, $charsetQuotes = false) {

		// print out or return result
		$this->display = $display;

		// local override of mutibyte string configuration
		if ($charsetHtml) {
			$this->charsetHtml = $charsetHtml;
		}
		else{
			$this->charsetHtml = self::$charsetHtmlStatic;
		}
		if ($charsetQuotes) {
			$this->charsetQuote = $charsetQuotes;
		}
		else {
			$this->charsetQuote = self::$charsetQuoteStatic;
		}

		// set mutibyte string character encoding
		mb_internal_encoding($this->charsetHtml);

		// check for cache directory
		if (!defined('SSP_CACHEDIRECTORY') or ! file_exists(SSP_CACHEDIRECTORY)) {
			$this->cacheEnabled = false;
		}

		if (!is_array($templateFile)) {
			// check file exists
			$this->templatePath = $this->tplExists($templateFile);
			$this->templateName = $templateFile;
			$templateArraySupplied = false;
		} else {
			$templateArraySupplied = true;
		}

		// check template data
		if (is_string($replaces)) {
			// just replaces the title
			$this->replaces["title"] = $replaces;
		} elseif (is_array($replaces)) {
			// set up array for many string replaces
			$this->replaces = $replaces;
		} elseif (is_object($replaces)) {
			// set up array for many string replaces
			$this->replaces = get_object_vars($replaces);
		} else {
			trigger_error("SSP_Template: Invalid template replacement data for " . $this->templatePath, E_USER_ERROR);
		}

		if (!$templateArraySupplied) {
			// load template and find length
			$this->templateArray = file($this->templatePath);
			if ($this->templateArray === false) {
				trigger_error("SSP_Template: Failed to open " . $this->templatePath, E_USER_ERROR);
			}
		} else {
			$this->templateArray = $templateFile;
		}
		$this->arrayLength = count($this->templateArray);
	}

	/**
	 * Find if template file exists in any of the template directories
	 * @param string $templateFile - name of template
	 * @return bool/string - false on not found else returns path to template
	 */
	public function tplExists($templateFile) {
		$foundfile = false;
		foreach (self::$paths as $path) {
			if (file_exists($path . $templateFile)) {
				$foundfile = true;
				$path = $path . $templateFile;
				break;
			}
		}

		if (!$foundfile) {
			// abort output if no template found
			trigger_error("SSP_Template: Template $templateFile does not exist in the following directories " . join(', ', self::$paths), E_USER_ERROR);
		}
		$this->templateName = $path;
		return($path);
	}

	/**
	 * Adds a path on which to find templates
	 * @param string $path - path to be added to the template search stack
	 */
	public static function addPath($path) {
		self::$paths[] = $path;
	}

	/**
	 * function includes template file until {$label} is reached
	 * the label has to be on its own on the line in the template
	 * also inserts the page title if it comes accross it.
	 *
	 * parameters
	 *  @lable - string - will stop processing on reaching this lable
	 */
	public function includeTill($lable = "end") {
		$returnedLines = 0;
		$this->exitLoop = false;
		// process down the template file
		while (!$this->exitLoop and $this->contentPosition < $this->arrayLength) {
			$value = $this->templateArray[$this->contentPosition];
			$this->skipOutput = false;

			// replace index tag, used to index tags items for data replacement etc.
			if ($this->replaceIndex) {
				$value = $this->replace('{0}', $this->indexNo, $value);
			}

			// process along the line
			$value = $this->processLine($value, $lable);

			// line output on meeting conditions
			if (!$this->exitLoop and !$this->skipOutput) {
				$returnedLines++;
				if ($returnedLines <= $this->numberReturnLines) {
					$this->returnedLines[] = $value;
				} else {
					$this->output .= $value;
					if ($this->display) {
						// output line to standard immediately
						echo $value;
					}
				}
				$this->contentPosition++;
			}
		} // end while process file array
		return $this->output;
	}

	/**
	 * Process a line to replace tags etc.
	 * @param string $value - line to be processed
	 * @param string $lable - lable to end processing
	 * @return string - resultant line
	 */
	private function processLine($value, $lable) {
		$this->valuePos = 0;
		while (!$this->exitLoop and ! $this->skipOutput and ! ($this->valuePos >= mb_strlen($value)) and $braceOpen = mb_strpos(" " . $value, '{', $this->valuePos)) {

			// see if there is possibly a tag on the line by checking for the close bracket
			if ($braceClose = mb_strpos($value, '}', $braceOpen)) {
				// extract tag
				$tag = mb_substr($value, $braceOpen, $braceClose - $braceOpen);
				// catch {} with no space problem
				if (!is_string($tag) or mb_strlen($tag) == 0) {
					$tag = " ";
				}
			} else {
				// break out of line processing if no close brackets and go to next line
				break;
			}

			if ($lable != "end" and $tag == $lable) {
				// stop template output at section insertion tag
				// used for programs that generate html for section insertion.
				$this->contentPosition++;
				$this->exitLoop = true;
			} elseif ($tag{0} == ":") {
				// process command, starts with {:
				$this->processCommand($tag, $braceClose);
			} elseif (array_key_exists($tag, $this->replaces)) {
				$value = $this->replaceTag($value, $tag);
			} else {
				// ensure brackets enclosed text is ingored when it is not replaced
				$this->valuePos = $braceOpen + 1;
			} // end of elsif block
		} // end while process line
		return $value;
	}
	
	/**
	 * Process a command tag
	 * @param type $tag
	 * @param type $braceClose
	 */
	private function processCommand($tag, $braceClose){
		$this->skipOutput = true; // don't display this and possibly other lines
		// found a command, format :command:tag
		$endColon = mb_strpos($tag, ':', 1);
		$command = mb_substr($tag, 1, $endColon - 1);
		$commandTag = trim(mb_substr($tag, $endColon + 1));
		// echo "Command Tag '". $command. "' '". $commandTag. "'";
		switch ($command) {
			case 'if': // check for tag exists
			case 'ifnot': // chack for tag not exisiting
				if (($command == "if" 
						and !array_key_exists($commandTag, $this->replaces)) 
						or ( $command == "ifnot" and array_key_exists($commandTag, $this->replaces))) {
					// 'if' tag does not exist in content array, fast forward to {:endif:tag}
					// 'ifnot' tag exists in content array, fast forward to {:endif:tag}
					$endLoop = '{:endif:' . $commandTag . '}';
					$searchPos = $this->contentPosition;
					do {
						$searchPos++;
						if ($searchPos >= $this->arrayLength) {
							trigger_error("SSP_Template: Conditional endif for $commandTag not found", E_USER_ERROR);
						}
						$value = $this->templateArray[$searchPos];
					} while (!mb_strpos(" " . $value, $endLoop));
					$this->contentPosition = $searchPos + 1;
				} else {
					// remove line with if command
					$this->contentPosition++;
				}
				break;

			case 'endif': // found endif flag during if execution
				// remove endif line
				$this->contentPosition++;
				break;

			case 'ne': // don't encode the specified tag
				$this->repFunctions[$commandTag] = "";
				$this->contentPosition++;
				break;

			case 'notne': // encode the specified tag
				if (isset($this->repFunctions[$commandTag])) {
					unset($this->repFunctions[$commandTag]);
				}
				$this->contentPosition++;
				break;

			case 'nl2br': // encode for special characters and then do nl2br
				$this->repFunctions[$commandTag] = "nl2br";
				$this->contentPosition++;
				break;

			case 'include': // include a text file at this position
				$this->skipOutput = false;
				$includePath = $this->tplExists($commandTag);
				if ($handle = fopen($includePath, "r")) {
					if ($includeContents = fread($handle, filesize($commandTag))) {
						$this->valuePos = $this->valuePos + mb_strlen($includeContents);
						$value = $this->replace('{:include:' . $commandTag . '}', $includeContents, $value);
					} else {
						trigger_error("SSP_Template: Failed to read $commandTag to include", E_USER_ERROR);
					}
					fclose($handle);
				} else {
					trigger_error("SSP_Template: Failed to open $commandTag to include", E_USER_ERROR);
				}
				break;

			case 'includet': // include a template file using supplied data
				$this->skipOutput = false;
				$includeTpl = new Template($this->replaces, $commandTag, false);
				$includeContents = $includeTpl->includeTill();
				$value = $this->replace('{:includet:' . $commandTag . '}', $includeContents, $value);
				$this->valuePos = $this->valuePos + mb_strlen($includeContents);
				break;

			case 'includeti': // use the supplied template object with internal data
				$this->skipOutput = false;
				if (isset($this->replaces[$commandTag]) and is_object($this->replaces[$commandTag]) and get_class($this->replaces[$commandTag]) === "w34u\ssp\Template") {
					$includeTpl = $this->replaces[$commandTag];
					$includeTpl->display = false;
					$includeTpl->restart($this->replaces);
					$includeContents = $includeTpl->output();
					$value = $this->replace('{:includeti:' . $commandTag . '}', $includeContents, $value);
					$this->valuePos = $this->valuePos + mb_strlen($includeContents);
				} else {
					trigger_error("SSP_Template: Invalide template object supplied for $commandTag", E_USER_ERROR);
				}
				break;

			default:
				// found no valid command, continue output
				$this->valuePos = $braceClose;
				$this->skipOutput = false;
		}
	}
	
	/**
	 * Replace tag with data
	 * @param string $value - line to be processed
	 * @param string $tag - tag to be replaced
	 */
	private function replaceTag($value, $tag){
		$data = $this->replaces[$tag];
		if (is_object($data)) {
			if (get_class($data) === "w34u\ssp\Template") {
				// if is a template object, return with string from that
				$data->display = false;
				$includeContents = $data->includeTill();
				$value = $this->replace('{' . $tag . '}', $includeContents, $value);
				$this->valuePos = $this->valuePos + mb_strlen($includeContents);
			} else {
				trigger_error("SSP_Template: Invalide template object supplied for $tag", E_USER_ERROR);
			}
		} elseif ($this->encode) {
			if (!array_key_exists($tag, $this->repFunctions)) {
				$replaceString = $this->he($data);
				$value = $this->replace('{' . $tag . '}', $replaceString, $value);
				$this->valuePos = $this->valuePos + mb_strlen($replaceString);
			} else {
				if (trim($this->repFunctions[$tag]) == "") {
					$value = $this->replace('{' . $tag . '}', $data, $value);
					$this->valuePos = $this->valuePos + mb_strlen($data);
				} elseif (trim($this->repFunctions[$tag]) == "nl2br") {
					$replaceString = nl2br($this->he($data));
					$value = $this->replace('{' . $tag . '}', $replaceString, $value);
					$this->valuePos = $this->valuePos + mb_strlen($replaceString);
				} else {
					$replaceString = ${$this->repFunctions[$tag]}($this->he($data));
					$value = $this->replace('{' . $tag . '}', $replaceString, $value);
					$this->valuePos = $this->valuePos + mb_strlen($replaceString);
				}
			}
		} else {
			if (!array_key_exists($tag, $this->repFunctions)) {
				$value = $this->replace('{' . $tag . '}', $data, $value);
				$this->valuePos = $this->valuePos + mb_strlen($data);
			} else {
				if (trim($this->repFunctions[$tag]) == "") {
					$replaceString = $this->he($data);
					$value = $this->replace('{' . $tag . '}', $replaceString, $value);
					$this->valuePos = $this->valuePos + mb_strlen($replaceString);
				} elseif (trim($this->repFunctions[$tag]) == "nl2br") {
					$replaceString = nl2br($this->he($data));
					$value = $this->replace('{' . $tag . '}', $replaceString, $value);
					$this->valuePos = $this->valuePos + mb_strlen($replaceString);
				} else {
					$replaceString = ${$this->repFunctions[$tag]}($this->he($data));
					$value = $this->replace('{' . $tag . '}', $replaceString, $value);
					$this->valuePos = $this->valuePos + mb_strlen($replaceString);
				}
			}
		}
		return $value;
	}

	/**
	 * Does include till without an exit tag
	 * @return string - result from template
	 */
	public function output() {
		return($this->includeTill());
	}

	function cache() {
		// check for cached file

		if ($this->cacheEnabled and $this->cache) {
			// check for and load template data if it exists
			if (file_exists($this->cachDir . $this->templateName . "_data")) {
				
			}
			// generate md5 for
		}
		return false;
	}

	/**
	 * Simply outputs the last last of the template from the current position.
	 * Use only if there are no further tags to be converted
	 * @return string - output from template
	 */
	public function displayFooter() {
		for ($i = $this->contentPosition; $i < $this->arrayLength; $i++) {
			if ($this->display) {
				echo $this->templateArray[$i];
			} else {
				$this->output .= $this->templateArray[$i];
			}
		}
		return $this->output;
	}

	/**
	 * re-starts the pointer at the top of the template with new data
	 * @param array/string $replaces - content replaces
	 * @param boolean $overwriteData - merge data if false
	 */
	public function restart($replaces, $overwriteData = false) {
		if (is_string($replaces)) {
			// just replaces the title
			$this->replaces["title"] = $replaces;
		} elseif (is_array($replaces)) {
			// set up array for many string replaces
			if (!$overwriteData) {
				$this->replaces = array_merge($this->replaces, $replaces);
			} else {
				$this->replaces = $replaces;
			}
		} elseif (is_object($replaces)) {
			// set up array for many string replaces
			if ($overwriteData) {
				$this->replaces = get_object_vars($replaces);
			} else {
				$this->replaces = array_merge($this->replaces, get_object_vars($replaces));
			}
		} else {
			echo "SSP_Template: Invalid template replacement data for " . $this->templatePath;
			exit();
		}
		$this->contentPosition = 0;
		$this->output = "";
	}

	/**
	 * add content to that produced so far
	 * @param string $pageContent - content to be added to that produced so far
	 */
	public function concat($pageContent) {
		$this->output .= $pageContent;
	}

	/**
	 * tells template not to encode the tag data and use a function
	 * @param string $tag - tag name
	 * @param string $function - function to be used on the tage content
	 */
	public function ne($tag, $function = "") {
		$this->repFunctions[$tag] = $function;
	}

	/**
	 * Gets the content of a file and puts it in a tag
	 * @param string $tag - tag to be reaplaced
	 * @param string $fileName - files name, is looked for in the tpl paths
	 * @param bool $ne - do not escape the contents of the file
	 */
	public function getFile($tag, $fileName, $ne = true) {
		$path = $this->tplExists($fileName);
		$data = file_get_contents($path);
		if (!$data) {
			trigger_error("SSP_Template: Failed to open $path for reading", E_USER_ERROR);
		}
		$this->replaces[$tag] = $data;
		if ($ne) {
			$this->ne($tag);
		}
	}

	/**
	 * adds or overwrites data for the template
	 * @param string $dataName - tag name
	 * @param string $value - value to replace
	 */
	public function setData($dataName, $value) {
		$this->replaces[$dataName] = $value;
	}

	/**
	 * adds a string to the title string to the page
	 * @param string $value - part of title to add
	 */
	public function titleAdd($value) {
		$this->replaces["title"] .= $value;
	}

	/**
	 * Do html entitles on a string
	 * @param string $string - string to html encoded
	 * @return encoded string
	 */
	private function he($string) {
		return htmlentities($string, $this->charsetQuote, $this->charsetHtml, false);
	}

	/**
	 * Do a multi-byte string replace
	 * @param string $replace - find this
	 * @param string $with - replace with
	 * @param string $string - string to change
	 * @return string - string changed
	 */
	private function replace($replace, $with, $string) {
		return mb_ereg_replace(preg_quote($replace), $with, $string);
	}

	/**
	 * Set the charset for the mb string functions and htmlentities
	 * @param string $charset
	 */
	public static function setCharset($charset){
		self::$charsetHtmlStatic = $charset;
	}
	
	/**
	 * Set encoding type for htmlentities
	 * @param string $charsetQuote
	 */
	public static function setCharesetQuote($charsetQuote){
		self::$charsetQuoteStatic = $charsetQuote;
	}
}

/* End of file Template.php */
/* Location: ./src/Template.php */