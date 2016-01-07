<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	SSP_setup.php
*   Created:	02-Dec-2010
*   Descrip:	Create templates and general functions for simple site protection admin.
*
*   Copyright 2005-2011 Julian Blundell, w34u
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
*   Rev. Date	02-Dec-2010
*   Descrip:	Created.
*/

class SSP_setup{

	/** @var SSP_Protect ssp session object */
	public $session;
	/** @var SSP_Configuration config object */
	public $cfg;
	/** @var SSP_DB database object */
	public $db;
	/** @var string seperator for page title */
	public $pageTitleSeperator = ' - ';
	/** @var array page title */
	private $pageTitleSegments = array();
	/** Template to be used
	 * @var string  */
	private $template = "sspgeneraltemplate.tpl";

	/**
	 * SSP site constructor
	 * @param SSP_Protect $session - protection object
	 * @param SSP_Configuration $cfg - configuration
	 * @param SSP_DB $db - database object
	 * @param bool $translateAdmin - load admin translation files
	 * @param string $template - main template name
	 */
	function __construct($session, $translateAdmin = false, $template = false){
		$this->session = $session;
		$this->cfg = SSP_Configuration::get_configuration();
		$this->db = SSP_DB::get_connection();
		
		if($this->cfg->translate and $translateAdmin){
			SSP_Protect::$tranlator->loadFile(false, 'admin');
		}
		
		if($template !== false){
			$this->template = $template;
		}
	}

	/**
	* creates a template for admin page displays
	* @param array $contentMain the pages content
	* @param string $tpl alternative template name
	* @param bool $createMenu create the main menu
	* @param bool $suppressLangSelect - suppress the language selection dropdown
	* @return SSP_Template main template
	*/
	function tpl($contentMain, $tpl="", $createMenu=true, $suppressLangSelect = false){

		// default to the main template if not other template not supplied
		if($tpl != ""){
			$template = $tpl;
		}
		else{
			$template = $this->template;
		}

		// if the content suppied is just a string use it as the page title
		if(is_string($contentMain)){
			$temp  = $contentMain;
			$contentMain = array();
			$contentMain["title"] = $temp;
		}

		// build the page title from the supplied segments
		if(count($this->pageTitleSegments)){
			if($this->session->isTranslate()) foreach($this->pageTitleSegments as $key => $titlePart){
				$this->pageTitleSegments[$key] = $this->session->t($titlePart);
			}
			$contentMain["title"] = $this->session->t($this->cfg->siteName) . $this->pageTitleSeperator. 
					implode($this->pageTitleSeperator, $this->pageTitleSegments);
		}
		else{
			$contentMain["title"]  = $this->session->t($this->cfg->siteName);
		}

        // add paths to various useful areas
        $contentMain["pathSite"] = $this->cfg->pathSite;
        $contentMain["pathAdmin"] = $this->cfg->adminDir;
		
		// create the language selection
		if($this->cfg->translate and !$suppressLangSelect){
			$formTemplate = array('<form action="{formAction}" method="post" id="languageSelectionform">',
					'{languageDropdown}',
					'{formHidden}',
					'</form>');
			$form = new SFC_Form(SSP_Path(true), 'notable', 'languageSelect');
			$form->translateDisable = true;
			$form->checkToken = false;
			$form->errorAutoFormDisplay = false;
			$form->formSubmitVar = 'languageSelectionformToken';
			$form->tplf = new SSP_Template("", $formTemplate);
			$languages = $this->session->getLanguages();
			$dropdownInformation = array();
			foreach ($languages as $lang => $languageInfo){
				$dropdownInformation[$lang] = array('text' => $languageInfo['description'], 'dir' => $languageInfo['dir'], 'class' => 'lang_'. $lang, 'style' => 'background-image: url(images/flag_'. $lang. '.png);');
			}
			$form->fe('select', 'languageDropdown', '', $dropdownInformation);
			$form->fep('deflt = '. SSP_Protect::$tranlator->getLanguage());
			$form->setParam('script', 'onChange="this.form.submit()"');
			if($form->processForm($_POST)){
				if(!$form->error){
					$this->session->lang = $form->getField('languageDropdown');
					session_write_close();
					//echo 'code '. $_SESSION['SSP_currentLanguageCode'];
					SSP_Divert(SSP_Path(true));
				}
			}
			$contentMain['languageSelectForm'] = $form->create();
		}

		if($createMenu){
			// generate main menu
			// highlight a main menu item
			if(isset($contentMain["mainSection"])){
				$section = $contentMain["mainSection"];
			}
			else{
				$section = "";
			}
			$url = $_SERVER["SCRIPT_NAME"];
			$menu = new menuGen();
			$menu->add($this->cfg->adminDir. 'useradmin.php?userId=' .$this->session->userId, $this->session->t("My Details"), strpos($url,"useradmin.php") !== false);
			$menu->add($this->cfg->adminDir.'adminusercreation.php', $this->session->t("New User"), strpos($url,"adminusercreation.php") !== false);
			$menu->add($this->cfg->userLister, $this->session->t("List Users"), strpos($url,"index.php") !== false);
			$menu->add($this->cfg->siteRoot, $this->session->t("Home"));
			$menu->add($this->cfg->logoffScript, $this->session->t("Log off"));
			$contentMain["mainMenu"] = $menu->cMenu();
		}
		else{
			$contentMain["mainMenu"] = "";
		}

		if(!isset($contentMain["menu"])){
			$contentMain["menu"] = "";
		}
		$tpl = new SSP_Template($contentMain, $template, false);
		return($tpl);
	}
	
	public function pageTitleAdd($titleSegment){
		$this->pageTitleSegments[] = $titleSegment;
	}
}
/* End of file SSP_setup.php */
/* Location: ./sspincludes/SSP_setup.php */