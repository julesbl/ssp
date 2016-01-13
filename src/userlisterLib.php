<?php
/*
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	Simple Site protection
*   Routine:	userlister.php
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

*/

/**
 * Filter values stored in session
 */
class memberFilter{
    // Filter SSP members for listing

    var $tables; // sql tables selected from
    var $where; // sql where condition
    var $order; // sql order condition
    var $alpha = "all"; // condition for alphabetical listing eg. a, b etc.
    var $alphaField = ""; // field on which the alphabetical condition is applied.
    var $filterFields = ""; // filters applied to fields array
    var $filterValues = ""; // filter strings for filters
    var $userAccess = "all"; // user type
    var $filterOr = 0; // use or condition on sql where statment
	var $filterOnFlags = 1; // filter using flags
    // flags can be filtered on 0, 1, 2 - 2 ignored status
    var $userDisabled = 0; // show disabled users
    var $userPending = 0; // show user pending program enable
    var $userAdminPending = 0; // show user pending admin
    var $creationFinished = 1; // show users whos creation has finished
    var $userWaiting = 0; // show users who need to reply to startup emails
	var $limit = "";
	var $listAlpha = "all a b c d e f g h i j k l m n o p q r s t u v w x y z";

	var $cfg; // configuration object

    function __construct($alphaField, $cfg){
        // contstructor
        //
        // params
        //  $alphaField - string - filed used for alpha filtering, also the first filter field in the filters
        $this->alphaField = $alphaField;
        $this->filterFields[0] = $alphaField;
        $this->filterValues[0] = "";

        $this->cfg = $cfg;
    }

	function newSearch(){
		// reset search parameters
		//

        $this->filterFields = array(0 => $this->alphaField);
        $this->filterValues = array(0 => "");
		$this->alpha = "all"; // condition for alphabetical listing eg. a, b etc.
		$this->userAccess = "all"; // user type
		$this->filterOr = 0; // use or condition on sql where statment
		$this->filterOnFlags = true; // filter using flags
		$this->userDisabled = 0; // show disabled users
		$this->userPending = 0; // show user pending program enable
		$this->userAdminPending = 0; // show user pending admin
		$this->creationFinished = 1; // show users whos creation has finished
		$this->userWaiting = 0; // show users who need to reply to startup emails
	}

	function displayFaulty(){
		// display user who are in some way faulty
		//

        $this->filterFields = array(0 => $this->alphaField);
        $this->filterValues = array(0 => "");
		$this->alpha = "all"; // condition for alphabetical listing eg. a, b etc.
		$this->userAccess = "all"; // user type
		$this->filterOr = 1; // use or condition on sql where statment
		$this->filterOnFlags = true; // filter using flags
		$this->userDisabled = 0; // show disabled users
		$this->userPending = 1; // show user pending program enable
		$this->userAdminPending = 1; // show user pending admin
		$this->creationFinished = 0; // show users whos creation has finished
		$this->userWaiting = 1; // show users who need to reply to startup emails
	}

	function displayAdminPending(){
		// display users who are admin pending

        $this->filterFields = array(0 => $this->alphaField);
        $this->filterValues = array(0 => "");
		$this->alpha = "all"; // condition for alphabetical listing eg. a, b etc.
		$this->userAccess = "all"; // user type
		$this->filterOr = 0; // use or condition on sql where statement
		$this->filterOnFlags = true; // filter using flags
		$this->userDisabled = 0; // show disabled users
		$this->userPending = 0; // show user pending program enable
		$this->userAdminPending = 1; // show user pending admin
		$this->creationFinished = 1; // show users whos creation has finished
		$this->userWaiting = 0; // show users who need to reply to startup emails
	}


    function update($data="", $updateTickbox = false){
        // takes parameters supplied to the script and modifies the
        // filters accordingly

        // parameters
        //	$data - array - array of data
        //	$updateTickbox - bool - update tick boxes

        if(!is_array($data)){
        	$dataUpdate = $_POST;
        }
        else{
        	$dataUpdate = $data;
        }

        // Alphabetical filter
        if(isset($_GET["alpha"])){
            $alpha=$_GET["alpha"];
            $setAlpha=true;
        }
        elseif(isset($dataUpdate["alpha"])){
            $alpha=$dataUpdate["alpha"];
            $setAlpha=true;
        }
        else{
            $setAlpha=false;
        }
        if($setAlpha){
            if(strpos(" ".$this->listAlpha, $alpha)){
                $this->alpha = $alpha;
            }
        }

        // set the field for alphabetical sorting
        if(isset($dataUpdate["alphaField"])){
            // sets up field for alpha field and sorting
            if(strlen($dataUpdate["alphaField"]) < 60){
                $this->alphaField=$dataUpdate["alphaField"];
            }
        }

		// set limit
		if(isset($dataUpdate["limit"])){
			$this->limit = $dataUpdate["limit"];
		}

        // like filters
        if(isset($dataUpdate["filterField0"])){
            // new search submission
            $filterNo = 0;
            while(isset($dataUpdate["filterField". $filterNo])){
			$filterField = "filterField". $filterNo;
                if(strlen($dataUpdate[$filterField]) < 100){
                    // save filter field
                    $this->filterFields[$filterNo]=$dataUpdate[$filterField];
					$filterValue = "filterValue". $filterNo;
                    if(isset($dataUpdate[$filterValue])){
                        if(strlen($dataUpdate[$filterValue]) < 100){
                            $this->filterValues[$filterNo] = $dataUpdate[$filterValue];
                        }
                    }
                }
                $filterNo++;
            }
        }

        if(isset($dataUpdate["filterOr"])){
            $this->filterOr = $dataUpdate["filterOr"];
        }

		// User access filter mod
		if(isset($dataUpdate["userAccess"])){
			$this->userAccess = $dataUpdate["userAccess"];
		}

		if($updateTickbox){
			if(isset($dataUpdate["filterOnFlags"])){
				$this->filterOnFlags = true;
			}
			else{
				$this->filterOnFlags = false;
			}
		}

		// flag checking
		if(isset($dataUpdate["userDisabled"])){
			$this->userDisabled = $dataUpdate["userDisabled"];
		}
		if(isset($dataUpdate["userPending"])){
			$this->userPending = $dataUpdate["userPending"];
		}
		if(isset($dataUpdate["userAdminPending"])){
			$this->userAdminPending = $dataUpdate["userAdminPending"];
		}
		if(isset($dataUpdate["userWaiting"])){
			$this->userWaiting = $dataUpdate["userWaiting"];
		}
		if(isset($dataUpdate["creationFinished"])){
			$this->creationFinished = $dataUpdate["creationFinished"];
		}
	}

	function addField(){
		// adds a new search field
		$this->filterFields[] = $this->alphaField;
		$this->filterValues[] = "";
	}
}

class userLister{

	/** @var Setup ssp setup object */
	var $ssp;
	var $filter; // user filter
	var $session; // session object
	var $command = ""; // commad used to invoke the routine
	var $cfg; // configuration object
	var $db; // database object

	/**
	 *
	 * @param Setup $ssp - Setup object
	 * @param string $command - latest command
	 */
	function __construct($ssp, $command){
		// constructor

		$this->ssp = $ssp;
		$this->session = $ssp->session;
		$this->command = $command;
		$this->cfg = $ssp->cfg;
		$this->db = $ssp->db;

		// check for and create the filter
		if(!isset($_SESSION["SSP_MemberFilter"])){
			$_SESSION["SSP_MemberFilter"]= new memberFilter($this->cfg->defaultAlpha, $this->cfg);
		}
		$this->filter =& $_SESSION["SSP_MemberFilter"];
	}

    function buildQuery($fields){
        // Builds the query based on the filters in place, and the columns selected.
        //
        // parameters
        //  $fields - fields to be listed in the table, misc table only

        // returns the a string - query for the database

        $query="select ";

        // selection to be displayed
        $sqlFields = "m.".implode(", m.",$this->db->qt($fields)).", u.UserId, ";
		// calculate if the user is valid
		$sqlFields .= "case when (";
		foreach($this->cfg->validUserFlags as $flag => $value){
			$flag = "u.". $this->db->qt($flag);
			if($value){
				$sqlFields .= "(1 - ". $flag. ") + ";
			}
			else{
				$sqlFields .= $flag. " + ";
			}
		}
		$sqlFields = substr($sqlFields, 0, -2);
        $sqlFields .= ") = 0 then u.UserAccess else 'Fault' end as UserStatus ";

        // tables
        $sqlTables = "from ".$this->cfg->userTable." u ";
        $sqlTables .= "left join ".$this->cfg->userMiscTable." m on u.". $this->db->qt("UserId") . "= m." . $this->db->qt("UserId") . " ";

        // where condition

        $sqlwhereCond = "where ";
        $where=array();
        $whereF=array();
		$sqlValues = array();

        // user access filtering
		if($this->filter->userAccess != "all"){
			$where[] = "u.". $this->db->qt("UserAccess"). " = ? ";
			$sqlValues[] = $this->filter->userAccess;
		}

        // alphabetical filtering
        if($this->filter->alpha != "all"){
            $where[]= $this->db->qt($this->filter->alphaField)." like ? ";
			$sqlValues[] = $this->filter->alpha. "%";
        }

        // get any filters specified
        foreach($this->filter->filterValues as $key => $value){
            if(trim($value) != ""){
                $whereF[]= $this->db->qt($this->filter->filterFields[$key]). " like ? ";
				$sqlValues[] = "%". $value. "%";
            }
        }

        // if one or more filters have been specified, create a section with the appropriate logic
        if(count($whereF)){
            $whereFilter = "(";
            if(!$this->filter->filterOr){
                $whereFilter .= implode("and ",$whereF);
            }
            else{
                $whereFilter .= implode("or ",$whereF);
            }
            $whereFilter .= ") ";
            $where[] = $whereFilter;
        }

        if($this->filter->filterOnFlags){
			if($this->filter->userDisabled != 2){
				$whereFl[]= "u.". $this->db->qt("UserDisabled"). " = ? ";
				$sqlValues[] = $this->filter->userDisabled;
			}
			if($this->filter->userPending != 2){
				$whereFl[]="u.". $this->db->qt("UserPending"). " = ? ";
				$sqlValues[] = $this->filter->userPending;
			}
			if($this->filter->userAdminPending != 2){
				$whereFl[]="u.". $this->db->qt("UserAdminPending"). " = ? ";
				$sqlValues[] = $this->filter->userAdminPending;
			}
			if($this->filter->creationFinished != 2){
				$whereFl[]="u.". $this->db->qt("CreationFinished"). " = ? ";
				$sqlValues[] = $this->filter->creationFinished;
			}
			if($this->filter->userWaiting != 2){
				$whereFl[]="u.". $this->db->qt("UserWaiting"). " = ? ";
				$sqlValues[] = $this->filter->userWaiting;
			}
			if(!$this->filter->filterOr){
				foreach($whereFl as $key => $value){
					$where[] = $value;
				}
			}
			else{
				$where[] = '('. implode('or ', $whereFl). ') ';
			}
        }

       if(count($where)){
            $sqlwhereCond .= implode("and ", $where);
       }
       else{
           $sqlwhereCond = " ";
       }

		// order list by the alpha field
        $sqlOrder ="order by ". $this->db->qt($this->filter->alphaField);

        $result = new stdClass();
		$result->sql = $query. $sqlFields. $sqlTables. $sqlwhereCond. $sqlOrder;
		$result->values = $sqlValues;

        return($result);
    }
	
	function displayFilterForm(){
		// display form to update filter values

		$form = new SfcForm($this->cfg->userLister, "noTable", "sspFilter");
		$form->tda("tpl", $this->tpl(array("title"=>"Modify search criteria")));
		$form->tda("tplf", "userListerSearchForm.tpl");
		$form->templateRoutine = "userLister::formFilterCreate";
		$form->tda("fields", $this->filter->filterFields);
		$form->fe("radio", "filterOr", "Select using", array(0=> "All", 1=>"Any"));
		$form->fep("dataType=int, deflt=". $this->filter->filterOr);
		foreach($this->filter->filterFields as $key => $value){
			$form->fe("select", "filterField". $key, "Search", $this->cfg->fieldsFilterList);
			$form->fep("deflt=$value");
			$form->fe("text", "filterValue". $key, "for");
			$form->fep("dataType=gen, deflt=". $this->filter->filterValues[$key]);
		}
		$form->fe("submit", "addField", "Add Search field");
		$form->fe("select", "limit", "Results per page", $this->cfg->limits);
		$form->fep("dataType=int, deflt=". $this->filter->limit);
		$accessList = array_merge(array("all" => "All Types"), $this->cfg->userAccessTypeDropdown);
		$form->fe("select", "userAccess", "Member Access", $accessList);
		$form->fep("dataType=password, deflt=". $this->filter->userAccess);
		$form->fe("check", "filterOnFlags", "Filter using flags", array(0,1));
		$form->fep("dataType=bin, deflt=". $this->filter->filterOnFlags);
		$flagFilterOptions = array(0 => " false", 1 => " true", 2 => " ignore");
		$form->fe("radio", "userDisabled", "Users who have been disabled", $flagFilterOptions);
		$form->fep("dataType=int, deflt=". $this->filter->userDisabled);
		$form->fe("radio", "userPending", "User who are waiting for external OK", $flagFilterOptions);
		$form->fep("dataType=int, deflt=". $this->filter->userPending);
		$form->fe("radio", "userAdminPending", "User Admin Pending", $flagFilterOptions);
		$form->fep("dataType=int, deflt=". $this->filter->userAdminPending);
		$form->fe("radio", "creationFinished", "User Properly created", $flagFilterOptions);
		$form->fep("dataType=int, deflt=". $this->filter->creationFinished);
		$form->fe("radio", "userWaiting", "Waiting for user to respond to email", $flagFilterOptions);
		$form->fep("dataType=int, deflt=". $this->filter->userWaiting);
		$form->fe("submit", "submit", "Search Now");
		$form->fe("submit", "newSearch", "Reset Search Criteria");
		$form->addHidden("command", $this->command);

		if($form->processForm($_POST)){
			if(!$form->error){
				$this->filter->filterOr = $form->getField("filterOr");
				foreach($this->filter->filterFields as $key => $value){
					$this->filter->filterFields[$key] = $form->getField("filterField". $key);
				}
				$this->filter->limit = $form->getField("limit");
				if(array_key_exists("addField", $form->data)){
					// add a new search field
					$this->filter->addField();
					SSP_Divert($this->cfg->userLister. "?command=filterChange");
				}
				elseif(array_key_exists("newSearch", $form->data)){
					// clears the form and search parameters
					$this->filter->newSearch();
					SSP_Divert($this->cfg->userLister. "?command=filterChange");
				}
				else{
					// show list with new search
					$this->filter->update($form->data, true);
					SSP_Divert($this->cfg->userLister);
				}
			}
		}
		else{
			echo $form->create();
		}
	}

	/**
	 * Process the form template to add the filter fields
	 * @param array form data and fields
	 * @return string - html for form
	 */
	function formFilterCreate($form){
		// create search form

		$fields = new Template($form, "userListerSearchFields.tpl");
		$fields->replaceIndex = true;
		$fields->encode = false;
		$fieldsHtml = "";
		foreach($form["fields"] as $key => $value){
			$fields->indexNo = $key;
			$fields->restart($form);
			$fieldsHtml .= $fields->output();
		}
		$form["searchFields"] = $fieldsHtml;
		
		$formPage = new Template($form, $form["tplf"]);
		$formPage->encode=false;
		
		$tpl = $form["tpl"];
		$tpl->display = false;
		$tpl->setData("content", $formPage->output());

		return($tpl->output());
	}


    function alphaFilter($selClass="here", $alphaClass="alphaFilter", $par=""){
        // creates a list of the alphabet filter
        //
        // parameters
        //  $alpha - string - current alpha selection
        //  $selClass - string class used to highlight selected alpha
        //	$par - string

        $letters = explode(" ",$this->filter->listAlpha);
        $string = '<ul class="'. $alphaClass. '">';

        foreach($letters as $letter){
            if($letter == $this->filter->alpha){
                $selection = ' class="'. $selClass. '"';
            }
            else{
                $selection = '';
            }
            $string.='<li'. $selection. '><a href="'.$this->cfg->userLister. '?alpha='. $letter. $par. '">'. $letter. '</a></li>';
        }
        $string .= '</ul>';
        return($string);
    }

	function lister(){
		// display list of users

		if(!isset($_SESSION["SSP_ListerSave"])){
			$_SESSION["SSP_ListerSave"]= new listerSave($this->cfg->limit);
		}
		$listerSave =& $_SESSION["SSP_ListerSave"];

		$listerSave->update();

		// build query
		$fields = array("FirstName", "FamilyName", 'TownCity');
		$queryInfo = $this->buildQuery($fields);

		$this->db->query($queryInfo->sql, $queryInfo->values, "User Lister: Getting list of users");

		$list = new lister($listerSave, $this->db, $this->cfg->userLister, 0, "&command=$this->command");
		$list->setLineFunction('listerLine', $this);

		$contentPage = array();
		$contentPage["title"] = "User List";
		$contentPage["alphFilter"] = $this->alphaFilter("here", "alphaFilter");
		$contentPage["pageNav"] = $list->pageNav();
		$lineContent["memberAdminUrl"] = $this->cfg->userAdminScript;
		$lineContent["userListerUrl"] = $this->cfg->userLister;
		$lineContent["currentUserId"] = $this->session->userId;


		$contentPage["list"] = $list->displayList($lineContent, "userListerLine.tpl", "userListerNoResult.tpl", "userListerOddLine.tpl",true);

		$page = new Template($contentPage, "userListerPage.tpl", false);
		$contentMain = array("title"=>"User list", "content"=>$page->output());
		$tpl = $this->tpl($contentMain);
		return $tpl->output();
	}

	public function listerLine($line){
		global $SSP_Config, $session;
		if($line["FirstName"] == "" and $line["FamilyName"] == ""){
			$line["FirstName"] = "no name";
		}
		if($line["TownCity"] == ""){
			$line["TownCity"] = " ";
		}
		$status = array('Fault' => 'User fault');
		$status = array_merge($status, $this->cfg->userAccessTypeDropdown);
		$line['UserStatus'] = $this->session->t($status[$line['UserStatus']]);
		// Disable delete for the current user
		if(strcmp($line["UserId"], $line["currentUserId"]) == 0){
			$line["noDelete"] = "";
		}
		return($line);
	}

	/**
	 * Delete a user
	 * @param type $userId
	 * @return string
	 */
    function deleteUser($userId){
    	// delete a user, not the current
		if(strcasecmp($userId, $this->session->userId) != 0){
			if(isset($_POST["deleteUser"])){
				$where = array("UserId"=>$userId);
				$this->db->delete($this->cfg->userMiscTable, $where, "SSP Admin: deleting user misc data");
				$this->db->delete($this->cfg->userTable, $where, "SSP Admin: deleting user login data");
				SSP_Divert($this->cfg->totalAdminScript);
			}
			elseif(isset($_POST["preserveUser"])){
				SSP_Divert($this->cfg->totalAdminScript);
			}
			else{
				// prompt to delete user
				$where = array("UserId"=>$userId);
				$user = $this->db->get($this->cfg->userMiscTable, $where, "SSP Admin: Getting data to prompt for user delete");
				if($user){
					$content = get_object_vars($user);
					$content["command"] = $this->command;
					$content["path"] = SSP_Path();
					$page = new Template($content, "userListerDeletePrompt.tpl", false);
					$mainContent = array();
					$mainContent["title"] = " - delete user ". $user->FirstName. " ". $user->FamilyName;
					$mainContent["content"] = $page->output();
					$tpl = $this->tpl($mainContent);
					return $tpl->output();
				}
				else{
					SSP_Divert($this->cfg->totalAdminScript);
				}
			}
		}
    }

	/**
	 *
	 * @param array $contentMain - template content data
	 * @return Template 
	 */
	function tpl($contentMain){
		// configure main template
		$this->ssp->pageTitleAdd("User admin");
		if(isset($contentMain["title"])){
			$this->ssp->pageTitleAdd($contentMain["title"]);
		}

		$menu = new menuGen();
		$menu->add($this->cfg->userLister.'?command=filterChange', $this->session->t("Modify Search"), $this->command=="filterChange");
		if($this->cfg->adminCheck){
			if(!($this->filter->userAdminPending == 1 and $this->filter->creationFinished == 1)){
				$menu->add($this->cfg->userLister.'?command=filterAdminPending', $this->session->t("List Admin Pending"));
			}
		}
		$menu->add($this->cfg->userLister.'?command=filterNormal', $this->session->t("Defualt Listing"));
		$menu->add('userlisterhelp.php', $this->session->t("Help"));
		$menu->sv("target=help");
		$contentMain["menu"] = $menu->cMenu();

		$tpl = $this->ssp->tpl($contentMain);
		return($tpl);
	}
}

/* End of file userlisterLib.php */
/* Location: ./sspincludes/userlisterLib.php */