<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	Translate.php
*   Created:	18/02/2013
*   Descrip:	Translation class for multilingual functions.
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
*   You should have received a copy of the The MIT License (MIT)
*   along with SSP; if not, view at
*   http://www.opensource.org; https://opensource.org/licenses/MIT
*
*   Revision:	a
*   Rev. Date	18/02/2013
*   Descrip:	Created.
*
*   Revision:	b
*   Rev. Date	12/1/2016
*   Descrip:	Changed to psr-4.
*
*/

namespace w34u\ssp;

class Translate{
	/** 
	 * array of strings to be translated
	 * @var array  */
	private static $translationStrings = array();
	/** 
	 * current language for translation
	 * @var string  */
	private $language = 'en';
	/** 
	 * path to the language directory
	 * @var string */
	private $loadPath = '';
	/** 
	 * default language for site
	 * @var string  */
	private $defaultLanguage = 'en';
	/** 
	 * in debug mode
	 * @var bool  */
	private static $debug = false;
	/**
	 * List of available languages
	 * @var array
	 */
	private $languageList = array();
	
	/**
	 * contructor
	 * @param string $langCode - language code
	 * @param array $languageList - list of available languages
	 * @param string $loadPath - path to load language files
	 */
	public function __construct($langCode, array $languageList, $loadPath) {
		$this->defaultLanguage = $langCode;
		$this->loadPath = $loadPath;
		foreach($languageList as $language){
			$fileName = 'lang_%s.conf.php';
			if(file_exists($loadPath. sprintf($fileName, $language))){
				require $loadPath. sprintf($fileName, $language);
			}
			else{
				SSP_error('SSP Language configuration file does not exist '. $loadPath. sprintf($fileName, $language));
			}
		}
		$this->setLanguage($langCode);
	}
	
	/**
	 * Load a language file
	 * @param string $langCode - language code
	 * @param string $postfix - string used for additional files
	 */
	public function loadFile($langCode = false, $postfix = false){
		if(!$langCode){
			$langCode = $this->getLanguage();
		}
		$languageFile = $this->loadPath;
		if(!$postfix){
			$languageFile .= 'lang_'. $langCode. '.php';
		}
		else{
			$languageFile .= 'lang_'. $langCode. '_'. $postfix. '.php';
		}
		if(isset(self::$translationStrings[$langCode])){
			if(file_exists($languageFile)){
				require($languageFile);
			}
			else{
				SSP_error('SSP Language file does not exist '. $languageFile);
			}
		}
		else{
			SSP_error('SSP Language not configured, cannot find language file '. $languageFile);
		}
	}

	/**
	 * Add a language to the language array
	 * @param string $langCode - language code for the translation
	 * @param string $langDescription - language description
	 * @param string $direction - html dir parameter for this language
	 */
	public static function setupLanguage($langCode, $langDescription, $direction){
		self::$translationStrings[$langCode]['description'] = $langDescription;
		self::$translationStrings[$langCode]['dir'] = $direction;
	}
	
	/**
	 * Add the language strings array to the language array
	 * @param string $langCode - language code
	 * @param array $strings - translation array for that language
	 */
	public static function setupStrings($langCode, array $strings){
		if(isset(self::$translationStrings[$langCode])){
			self::$translationStrings[$langCode]['strings'] = $strings;
		}
		else{
			SSP_error('SSP Language not configured, cannot load strings for '. $langCode);
		}
	}
	
	/**
	 * Add more translations strings to a language
	 * @param type $langCode
	 * @param array $strings
	 * @return bool returns true on success
	 */
	public static function addToLanguage($langCode, array $strings){
		if(isset(self::$translationStrings[$langCode])){
			if(isset(self::$translationStrings[$langCode]['strings'])){
				// merges new translations with existing
				self::$translationStrings[$langCode]['strings'] = array_merge(self::$translationStrings[$langCode]['strings'], $strings);
			}
			else{
				self::$translationStrings[$langCode]['strings'] = $strings;
			}
		}
		else{
			SSP_error('SSP Language not configured, cannot load extra strings for '. $langCode);
		}
	}
	
	/**
	 * Set language
	 * @param string $lang
	 */
	public function setLanguage($lang){
		$this->language = $lang;
		// attempt to load the language if not already loaded
		if(!isset(self::$translationStrings[$lang]['strings'])){
			$this->loadFile($lang);
		}
	}
	
	/**
	 * Return default language
	 * @return string
	 */
	public function getLanguage(){
		return($this->language);
	}
	
	/**
	 * returns an array of the configured languages
	 * @return array
	 */
	public function getLanguages(){
		$result = array();
		foreach(self::$translationStrings as $code => $data){
			$result[$code] = $data['description'];
		}
		return($result);
	}
	
	/**
	 * Returns a list of the configured languages for display purposes, includes the html direction
	 * @return array - list of languages for display
	 */
	public function getLangDisplay(){
		$result = array();
		foreach(self::$translationStrings as $code => $data){
			$result[$code]['description'] = $data['description'];
			$result[$code]['dir'] = $data['dir'];
		}
		return($result);
	}
	
	/**
	 * Translate a peice of text
	 * @param string $text - to be translated
	 * @param bool $useSiteDefault - use the site default language
	 */
	public function t($text, $useSiteDefault = false){
		if(!$useSiteDefault){
			/* @var $lang string */
			$lang = $this->language;
		}
		else{
			$lang = $this->defaultLanguage;
		}
		if(isset(self::$translationStrings[$lang]) and isset(self::$translationStrings[$lang]['strings'][$text])){
			// translation string found
			if(self::$debug){
				$pre = 'Y_';
			}
			else{
				$pre = "";
			}
			return($pre. self::$translationStrings[$lang]['strings'][$text]);
		}
		else{
			// no translation string found so returns orriginal
			if(self::$debug){
				$pre = 'N_';
			}
			else{
				$pre = "";
			}
			return($pre. $text);
		}
	}
	
	/**
	 * Sets the translation into debug mode
	 */
	public static function debug(){
		self::$debug = true;
	}


	/**
	 * Detect the browser language and resturn the nearest configured one available
	 * @return string - language to use
	 */
	public function detectBrowserLanguage(){
		if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])){
			$languages = $this->parseBrowserLanguages($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
			// check if we have an exact match
			$installedLanguages = $this->getLanguages();
			$foundLanguages = array();
			foreach($languages as $lang => $priority){
				if(in_array($lang, $installedLanguages)){
					$foundLanguages[$lang] = $priority;
				}
			}
			// try and find partial match
			$partial = false;
			if(!count($foundLanguages)){
				foreach($languages as $lang => $priority){
					$partialLang = substr($lang, 0, 2);
					if(strlen($lang) > 2 and isset($installedLanguages[$partialLang])){
						$foundLanguages[$lang] = $priority;
						$partial = true;
					}
				}
			}
			// set language to found language
			if(count($foundLanguages)){
				// get highest priority of found languages
				array_multisort($foundLanguages, SORT_DESC);
				$keys = array_keys($foundLanguages);
				$language = array_shift($keys);
				if($partial){
					$language = substr($language, 0, 2);
				}
				return($language);
			}
			else{
				return($this->language);
			}
		}
		else{
			return($this->language);
		}
	}
	
	/**
	 * Parses the browsers accepted languages and returns an array, most liked first
	 * @param string $http_accept - browser languages e.g. es,en-us;q=0.3,de;q=0.1
	 * @return array - languages, most liked first
	 */
	public function parseBrowserLanguages($http_accept){
		$languages = array();
		if(strlen($http_accept) > 1){
			# Split possible languages into array
			$x = explode(",",$http_accept);
			foreach ($x as $val) {
			   #check for q-value and create associative array. No q-value means 1 by rule
			   if(preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i",$val,$matches))
				  $lang[$matches[1]] = (float)$matches[2];
			   else
				  $lang[$val] = 1.0;
			}
			array_multisort($lang, SORT_DESC);
		}
		return($lang);
	}
}

/* End of file Translate.php */
/* Location: ./src/Translate.php */