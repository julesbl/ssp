<?php
/*
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site protection
*   Routine:	MemberFilter.php
*   Created:	18/01/2016
*   Descrip:	Class for filtering membership list, moved from UserLister.php.
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
*
*   Revision:	b
*   Rev. Date	30/11/2006
*   Descrip:	memberFilter and lister streamlined using templates and SFP forms.
*
*   Revision:	c
*   Rev. Date	14/01/2016
*   Descrip:	Composer implemented.
*/

namespace w34u\ssp;

/**
 * Filter values stored in session
 */
class MemberFilter{
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
	public $listAlphaAll = 'all';

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

/* End of file MemberFilter.php */
/* Location: ./src/MemberFilter.php */