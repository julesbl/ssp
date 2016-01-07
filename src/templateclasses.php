<?php
/*
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	    Simple Site Protection
*   Routine:	templateclasses.php
*   Created:	23/09/2005
*   Descrip:	Classes to implement basic templating functions for the SSP applications.
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
*/

class SSP_Template{
    // class to implement a template for the admin programs

    var $templateName = ""; // name of the template
    var $templatePath = ""; // path to template
    var $templateArray = array(); // template loaded into this array
    var $arrayLength = 0; // number of lines in template
    var $contentPosition = 0; // Current line being processed
    var $replaces = array(); // array of strings to be replaced within the file
    var $repFunctions = array(); // functions to be applied to particular field, if "" no htmlentites
	var $encode = true; // automatically does htmlentities on replacement strings
    var $display = true; // output to stdout if true
    var $output = ""; // contains resulting page
	var $replaceIndex = false; // replace the index tag
	var $indexNo = "0"; // index tag to replace {0} in any tag.
	var $numberReturnLines = 0; // number of lines to be returned before output begins
	var $returnedLines = array(); // lines returned before output

	// control htmlentities function
	var $charsetHtml = "UTF-8";
	var $charsetQuote = ENT_QUOTES;

	var $cache = false; // always cache this template
	var $cacheEnabled = false; // cacheing is enabled
	var $cachDir = ""; // cache directory
	
	/** @var array of string - set of paths in which to find templates */
	private static $paths = array(
		'./',
		'templates/'
	);

    // Constants
    //  SSP_TEMPLATEPATH - path to template directory

	/**
	* Constructor
	*
	* @param type $name Description
	* @param string/array $replaces - either the title or an array of elements to be replaced
	* @param string/array $templateFile - template file to be used or array of text to be used as a template
	* @param bool $display - sends directly to STDOUT if true
	*  
	*/
    function __construct($replaces, $templateFile, $display = false, $charsetHtml = false, $charsetQuotes = false){

        // print out or return result
        $this->display = $display;
		
		// local override of mutibyte string configuration
		if($charsetHtml){
			$this->charsetHtml = $charsetHtml;
		}
		if($charsetQuotes){
			$this->charsetQuote = $charsetQuotes;
		}
		
		// set mutibyte string character encoding
		mb_internal_encoding($this->charsetHtml);

        // check for cache directory
        if(!defined('SSP_CACHEDIRECTORY') or !file_exists(SSP_CACHEDIRECTORY)){
        	$this->cacheEnabled = false;
        }

		if(!is_array($templateFile)){
			// check file exists
			$this->templatePath = $this->tplExists($templateFile);
			$this->templateName = $templateFile;
			$templateArraySupplied = false;
		}
		else{
			$templateArraySupplied = true;
		}

		// check template data
		if(is_string($replaces)){
			// just replaces the title
			$this->replaces["title"] = $replaces;
		}
		elseif(is_array($replaces)){
			// set up array for many string replaces
			$this->replaces = $replaces;
		}
		elseif(is_object($replaces)){
			// set up array for many string replaces
			$this->replaces = get_object_vars($replaces);
		}
		else{
			echo "SSP_Template: Invalid template replacement data for ". $this->templatePath;
			exit();
		}

		if(!$templateArraySupplied){
			// load template and find length
			$this->templateArray = file($this->templatePath);
			if($this->templateArray === false){
				die("SSP_Template: Failed to open ". $this->templatePath);
			}
		}
		else{
			$this->templateArray = $templateFile;
		}
		$this->arrayLength = count($this->templateArray);
    }

    /**
	 * Find if template file exists in any of the template directories
	 * @param string $templateFile - name of template
	 * @return bool/string - false on not found else returns path to template
	 */
	public function tplExists($templateFile){
        $foundfile = false;
		foreach(self::$paths as $path){
			if(file_exists($path. $templateFile)){
				$foundfile = true;
				$path = $path. $templateFile;
				break;
			}
		}
		
        if(!$foundfile){
            // abort output if no template found
            echo "SSP_Template: Template $templateFile does not exist in the following directories ". join(', ', self::$paths);
            exit();
        }
        $this->templateName = $path;
        return($path);
    }
	
	/**
	 * Adds a path on which to find templates
	 * @param string $path - path to be added to the template search stack
	 */
	public static function addPath($path){
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
    function includeTill($lable = "end"){

        $exitLoop = false;
		$returnedLines = 0;
        // process down the template file
        while(!$exitLoop and $this->contentPosition < $this->arrayLength){
            $value = $this->templateArray[$this->contentPosition];
            $valuePos = 0;
            $skipOutput = false;

			// replace index tag, used to index tags items for data replacement etc.
			if($this->replaceIndex){
				$value = $this->replace('{0}', $this->indexNo, $value);
			}
			
            // process along the line
            while(!$exitLoop and !$skipOutput and !($valuePos >= mb_strlen($value)) and $braceOpen = mb_strpos(" ".$value, '{', $valuePos)){

                // see if there is possibly a tag on the line by checking for the close bracket
                if($braceClose = mb_strpos($value, '}', $braceOpen)){
                	// extract tag
					$tag = mb_substr($value, $braceOpen, $braceClose - $braceOpen);
					// catch {} with no space problem
					if(!is_string($tag) or mb_strlen($tag) == 0){
						$tag = " ";
					}
				}
				else{
					// break out of line processing if no close brackets and go to next line
					break;
				}

                if($lable != "end" and $tag == $lable){
                    // stop template output at section insertion tag
					// used for programs that generate html for section insertion.
                    $this->contentPosition++;
                    $exitLoop = true;
                }
                elseif($tag{0} == ":"){
                	// process command, starts with {:
                    $skipOutput = true; // don't display this and possibly other lines
                    // found a command, format :command:tag
                    $endColon = mb_strpos($tag, ':', 1);
                    $command = mb_substr($tag, 1, $endColon-1);
                    $commandTag = trim(mb_substr($tag, $endColon+1));
                    // echo "Command Tag '". $command. "' '". $commandTag. "'";
                    switch($command) {
                    	case 'if': // check for tag exists
                    	case 'ifnot': // chack for tag not exisiting
                            if(($command == "if" and !array_key_exists($commandTag, $this->replaces)) or ($command == "ifnot" and array_key_exists($commandTag, $this->replaces))){
                                // 'if' tag does not exist in content array, fast forward to {:endif:tag}
                                // 'ifnot' tag exists in content array, fast forward to {:endif:tag}
                                $endLoop = '{:endif:'. $commandTag. '}';
								$searchPos = $this->contentPosition;
                                do{
                                    $searchPos++;
									if($searchPos >= $this->arrayLength){
										die("SSP_Template: Conditional endif for $commandTag not found");
									}
                                    $value=$this->templateArray[$searchPos];
                                }while(!mb_strpos(" ". $value, $endLoop));
                                $this->contentPosition = $searchPos + 1;
                            }
                            else{
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
							if(isset($this->repFunctions[$commandTag])){
								unset($this->repFunctions[$commandTag]);
							}
                        	$this->contentPosition++;
                   		break;

						case 'nl2br': // encode for special characters and then do nl2br
							$this->repFunctions[$commandTag] = "nl2br";
							$this->contentPosition++;
						break;

                   		case 'include': // include a text file at this position
                            $skipOutput = false;
                   			$includePath = $this->tplExists($commandTag);
                   			if($handle = fopen($commandTag, "r")){
								if($includeContents = fread($handle, filesize($commandTag))){
									$valuePos = $valuePos + mb_strlen($includeContents);
									$value = $this->replace('{:include:'.$commandTag.'}', $includeContents, $value);
								}
								else{
									die("SSP_Template: Failed to read $commandTag to include");
								}
								fclose($handle);
							}
							else{
								die("SSP_Template: Failed to open $commandTag to include");
							}
                   		break;

                   		case 'includet': // include a template file using supplied data
                            $skipOutput = false;
                   			$includeTpl = new SSP_Template($this->replaces, $commandTag, false);
                   			$includeContents = $includeTpl->includeTill();
							$value = $this->replace('{:includet:'.$commandTag.'}', $includeContents, $value);
							$valuePos = $valuePos + mb_strlen($includeContents);
                   		break;

						case 'includeti': // use the supplied template object with internal data
							$skipOutput = false;
							if(isset($this->replaces[$commandTag]) and is_object($this->replaces[$commandTag]) and strtolower(get_class($this->replaces[$commandTag])) == "ssp_template"){
								$includeTpl = $this->replaces[$commandTag];
								$includeTpl->display = false;
								$includeTpl->restart($this->replaces);
								$includeContents = $includeTpl->output();
								$value = $this->replace('{:includeti:'.$commandTag.'}', $includeContents, $value);
								$valuePos = $valuePos + mb_strlen($includeContents);
							}
							else{
								die("SSP_Template: Invalide template object supplied for $commandTag");
							}
						break;

                        default:
                        	// found no valid command, continue output
							$valuePos = $braceClose;
                            $skipOutput = false;
                    }
                }
                elseif(array_key_exists($tag, $this->replaces)){
                	$data = $this->replaces[$tag];
					if(is_object($data) and strtolower(get_class($data)) == "ssp_template"){
						// if is a template object, return with string from that
						$data->display = false;
						$includeContents = $data->includeTill();
						$value = $this->replace('{'.$tag.'}', $includeContents, $value);
						$valuePos = $valuePos + mb_strlen($includeContents);
					}
					elseif($this->encode){
						if(!array_key_exists($tag, $this->repFunctions)){
							$replaceString = $this->he($data);
							$value = $this->replace('{'.$tag.'}', $replaceString, $value);
							$valuePos = $valuePos + mb_strlen($replaceString);
						}
						else{
							if(trim($this->repFunctions[$tag]) == ""){
								$value = $this->replace('{'.$tag.'}', $data, $value);
								$valuePos = $valuePos + mb_strlen($data);
							}
							elseif(trim($this->repFunctions[$tag]) == "nl2br"){
								$replaceString = nl2br($this->he($data));
								$value = $this->replace('{'.$tag.'}', $replaceString, $value);
								$valuePos = $valuePos + mb_strlen($replaceString);
							}
							else{
								$replaceString = ${$this->repFunctions[$tag]}($this->he($data));
								$value = $this->replace('{'.$tag.'}', $replaceString, $value);
								$valuePos = $valuePos + mb_strlen($replaceString);
							}
						}
					}
					else{
						if(!array_key_exists($tag, $this->repFunctions)){
							$value = $this->replace('{'.$tag.'}', $data, $value);
							$valuePos = $valuePos + mb_strlen($data);
						}
						else{
							if(trim($this->repFunctions[$tag]) == ""){
								$replaceString = $this->he($data);
								$value = $this->replace('{'.$tag.'}', $replaceString, $value);
								$valuePos = $valuePos + mb_strlen($replaceString);
							}
							elseif(trim($this->repFunctions[$tag]) == "nl2br"){
								$replaceString = nl2br($this->he($data));
								$value = $this->replace('{'.$tag.'}', $replaceString, $value);
								$valuePos = $valuePos + mb_strlen($replaceString);
							}
							else{
								$replaceString = ${$this->repFunctions[$tag]}($this->he($data));
								$value = $this->replace('{'.$tag.'}', $replaceString, $value);
								$valuePos = $valuePos + mb_strlen($replaceString);
							}
						}
					}
				}
				else{
					// ensure brackets enclosed text is ingored when it is not replaced
					$valuePos = $braceOpen + 1;

			   } // end of elsif block
            } // end while process line

			// line output on meeting conditions
            if(!$exitLoop and !$skipOutput){
				$returnedLines++;
				if($returnedLines <= $this->numberReturnLines){
					$this->returnedLines[] = $value;
				}
				else{
					$this->output .= $value;
					if($this->display){
						// output line to standard immediately
						echo $value;
					}
				}
                $this->contentPosition++;
            }
        } // end while process file array
		return($this->output);
    }

    function output(){
    	// just does an includeTill without a tag

    	return($this->includeTill());
    }

    function cache(){
    	// check for cached file

    	if($this->cacheEnabled and $this->cache){
    		// check for and load template data if it exists
    		if(file_exists($this->cachDir. $this->templateName. "_data")){

    		}
    		// generate md5 for
    	}
    	return(false);
    }

    function displayFooter() {
        // Simply outputs the last last of the template from the current position.
        // Use only if there are no further tags to be converted

        for($i=$this->contentPosition; $i < $this->arrayLength; $i++){
            if($this->display){
                echo $this->templateArray[$i];
            }
            else{
                $this->output .= $this->templateArray[$i];
            }
        }
        return($this->output);
    }

    function restart($replaces, $overwriteData = false){
        // re-starts the pointer at the top of the template with new data

        if(is_string($replaces)){
            // just replaces the title
            $this->replaces["title"] = $replaces;
        }
        elseif(is_array($replaces)){
            // set up array for many string replaces
            if(!$overwriteData){
            	$this->replaces = array_merge($this->replaces, $replaces);
            }
            else{
            	$this->replaces = $replaces;
            }
        }
		elseif(is_object($replaces)){
			// set up array for many string replaces
			if($overwriteData){
				$this->replaces = get_object_vars($replaces);
			}
			else{
				$this->replaces = array_merge($this->replaces, get_object_vars($replaces));
            }
        }
        else{
            echo "SSP_Template: Invalid template replacement data for ". $this->templatePath;
            exit();
        }
        $this->contentPosition = 0;
		$this->output = "";
    }

    function concat($pageContent){
    	// add content to that produced so far
    	//
    	// parameters
    	//	$pageContent - string - content to be added to that produced so far

    	$this->output .= $pageContent;
    }

    function ne($tag, $function=""){
    	// tells template not to encode the tag data

    	$this->repFunctions[$tag] = $function;
    }

    /**
	 * Gets the content of a file and puts it in a tag
	 * @param string $tag - tag to be reaplaced
	 * @param string $fileName - files name, is looked for in the tpl paths
	 * @param bool $ne - do not escape the contents of the file
	 */
	function getFile($tag, $fileName, $ne=true){
    	// gets a file and puts the contents into the data using the tag as an index

    	$path = $this->tplExists($fileName);
    	$data = file_get_contents($path);
    	if(!$data){
    		die("SSP_Template: Failed to open $path for reading");
    	}
    	$this->replaces[$tag] = $data;
    	if($ne){
    		$this->ne($tag);
    	}
    }

    function setData($dataName, $value){
    	// adds or overwrites data for the template

    	$this->replaces[$dataName] = $value;
    }

    function titleAdd($value){
    	// adds a string ontoi the page title

    	$this->replaces["title"] .= $value;
    }

	function he($string){
		return(htmlentities($string, $this->charsetQuote, $this->charsetHtml, false));
	}
	
	function replace($replace, $with, $string){
		return(mb_ereg_replace(preg_quote($replace), $with, $string));
	}
}

/* End of file templateclasses.php */
/* Location: ./sspincludes/templateclasses.php */