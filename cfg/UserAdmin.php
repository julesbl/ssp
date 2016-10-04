<?php
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)7833 512221
*
*   Project:	Simple Site Protection
*   Routine:	UserAdmin.php
*   Created:	14/01/2016
*   Descrip:	
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
*   Rev. Date	12/01/2016
*   Descrip:	Class moved from a singe file with multiple classes.
*/

namespace w34u\ssp;

class UserAdmin extends UserAdminBase{
    /**
     * Create user admin template
     * @param array $contentMain - template content data
     * @param bool $noMenusAndInfo - don't show user info
     * @return Template 
     */
    public function tpl($contentMain, $noMenusAndInfo = false){
        // configure main template
        $this->ssp->pageTitleAdd("Member admin");
        if($this->id != "" and !$noMenusAndInfo){
            $name = $this->getName($this->id);
            $this->ssp->pageTitleAdd($name);
            $contentMain["displayName"] = $name;
        }
        if(isset($contentMain["title"])){
            $this->ssp->pageTitleAdd($contentMain["title"]);
        }

        // generate menu
        if($this->generateMenus and !$noMenusAndInfo){
            $menu = new MenuGen();
            $path = $this->cfg->userAdminScript;
			$url = $_SERVER['REQUEST_URI'];
            $menu->add($path. "/chInfo", $this->session->t("Change Info"), strpos($url, "chInfo") !== false);
            $menu->sv("title={$this->session->t('Change user information')}");
            $menu->add($path. "/chPswd", $this->session->t("Change Password"), strpos($url, "chPswd") !== false);
            $menu->sv("title={$this->session->t('Change user password')}");
            $menu->add($path. "/chEmail", $this->session->t("Change Email"), strpos($url, "chEmail") !== false);
            $menu->sv("title={$this->session->t('Change user email')}");
            $menu->add($path. "/info/". $this->id, $this->session->t("Basic Info"), strpos($url, "info") !== false);
            $menu->sv("title={$this->session->t('Show basic information')}");
            $menu->add("", "");
            if($this->admin){
                $menu->add($path. "/advInfo", $this->session->t("Advanced Info"), strpos($url, "advInfo") !== false);
                $menu->sv("title={$this->session->t('Show advanced information')}");
                $menu->add($path. "/chAdv", $this->session->t("Change Advanced"), strpos($url, "chAdv") !== false);
                $menu->sv("title={$this->session->t('Change advanced information')}");
                $menu->add($path. "/joinEmail", $this->session->t("Send Joining Email"), strpos($url, "joinEmail") !== false);
                $menu->sv("title={$this->session->t('Send a joinup email to the user')}");
                $menu->add($path. "/email", $this->session->t("Email Member"), strpos($url, "email") !== false);
                $menu->sv("title={$this->session->t('Email the member')}");
                $menu->add("", "");
            }
            $menu->add("useradminhelp.php", $this->session->t("Help"));
            $menu->sv("target=_blank");
            $contentMain["menu"] = $menu->cMenu();
        }

        $tpl = $this->ssp->tpl($contentMain, $this->templateFile, $this->generateMenus);
        return($tpl);
    }
    
    /**
     * User joinup function
     */
    public function userJoin(){
        if($this->cfg->confirmType==0 or $this->cfg->confirmType==3){
            $needPassword = true;
        }
        else{
            $needPassword = false;
        }

        $form = new sfc\Form(SSP_Path(), $this->cfg->userTable, "userJoin");
        $form->tpl = $this->tpl(array("title" => "Join SSP"), true);
		$form->errorAutoFormDisplay = false;
        if($this->subTpl != ""){
            $form->tplf = $this->subTpl;
        }
        else{
            $form->tplf = "userJoin.tpl";
        }
        $form->fe("text", "firstName", "First name");
        $form->fep("width=30, required=true");
        $form->fe("text", "lastName", "Last name");
        $form->fep("width=30, required=true");
        $form->fe("text", "email", "Your email");
        $form->fep("width=30,required=true, dataType=email");

        if($this->cfg->loginType == 1 or $this->cfg->getUserName){
            $form->fe("text", "name", "User name");
            $form->fep("width=15,required=true,dataType=password");
        }
        if($needPassword){
            $form->fe("password", "password", "Your password");
            $form->fep("width=15, required=true, dataType=password, minChar=". $this->cfg->minPassword);

            $form->fe("password", "password2", "Enter password again");
            $form->fep("width=15,sql=false,dataType=password,required=true");
        }
        if($this->cfg->userHasSignUpOptions){
            // user has a set of options to sign up
            $form->fe("select", "signUpLevel", "Type of membership", $this->cfg->userAccessSignUpDropdown);
            $form->fep("dataType=int, sql=false");
        }
        $form->tda("loginPath", $this->cfg->logonScript);

        if($form->processForm($_POST)){
            if(!$form->error){
                $form->setField("email", strtolower($form->getField("email")));
                if($this->userCreateCheck($form)){
                    return $form->create(true);
                }
                else{
                    $loginData = array();
                    $userId = SSP_uniqueId();
                    $loginData["UserId"] = $userId;
                    $loginData["UserEmail"] = $form->getField("email");

                    if($needPassword){
                        $loginData["UserPassword"] = $this->session->cryptPassword($form->getField("password"));
                    }
                    if($this->cfg->userHasSignUpOptions){
                        if(isset($this->cfg->userAccessSignUpLevels[$form->getField("signUpLevel")])){
                            $loginData["UserAccess"] = $this->cfg->userAccessSignUpLevels[$form->getField("signUpLevel")];
                        }
                        else{
                            $loginData["UserAccess"]=$this->cfg->userDefault;
                        }
                    }
                    else{
                        $loginData["UserAccess"]=$this->cfg->userDefault;
                    }
                    if($this->cfg->adminCheck){
                        $loginData["UserAdminPending"]=1;
                    }
                    if($this->cfg->confirmType != 0){
                        $loginData["UserWaiting"]=1;
                    }
                    if($this->cfg->furtherProgram){
                        $loginData["UserPending"]=1;
                    }
                    // create login record
                    $this->db->insert($this->cfg->userTable, $loginData, "Inserting new member login data");
                    $miscData = array();
                    $miscData["UserId"] = $userId;
                    $miscData["FirstName"] = $form->getField("firstName");
                    $miscData["FamilyName"] = $form->getField("lastName");
                    $this->db->insert($this->cfg->userMiscTable, $miscData, "Inserting new member misc data");
                    $this->id = $userId;
                    $this->userFinish();
                    return $this->welcomeScreen();
                }
            }
			else{
				return $form->create(true);
			}
        }
        else{
            return $form->create();
        }
    }

    /**
     * Get logged in users name
     * @param string $userId - user id
     * @param bool $encode - encode the user name using html entities
     * @return string - users name
     */
    public function getName($userId, $encode=true){
        $fields = $this->cfg->displayNameFields;
        $format = $this->cfg->displayNameFormat;
        $where = array("UserId"=>$userId);
        if($result = $this->db->getf($this->cfg->userMiscTable, $fields, $where, "SSP Admin: Getting user name")){
            $name = vsprintf($format, get_object_vars($result));
            if($encode){
                return(htmlentities($name));
            }
            else{
                return($name);
            }
        }
        else{
            return("");
        }
    }

    /**
     * Return formatted user mis information
     * @return string
     */
    public function displayMisc(){
        // displays Misc user info

        $content = $this->getMisc("*", "SSP Admin: Database error on misc data fetch for display ");
        $page = new Template(get_object_vars($content), "displaymisc.tpl", false);
        $page->repFunctions["Address"] = "nl2br";
        $contentMain["content"] = $page->output();
        $contentMain["title"] = "User information";
        $tpl = $this->tpl($contentMain);
        return $tpl->output();
   }

   /**
    * Edit Miscellaneous data
    * @param bool $creating - creating a new user
    * @param bool $reDisplay - re-display after update
    * @return string/bool - edit screen or true on save and not re-display
    */ 
    public function userMisc($creating=false, $reDisplay=false){
        // Form elements
        $form= new sfc\Form(SSP_Path(), $this->cfg->userMiscTable, "userMisc");
        $form->errorAutoFormDisplay = false;
        $mainContent = array();
        if($creating){
            $mainContent["title"] = "Miscellaneous information";
        }
        else{
            $mainContent["title"] = "Edit info";
        }
        $form->tpl = $this->tpl($mainContent, $creating);
        if($this->subTpl != ""){
            $form->tplf = $this->subTpl;
        }
        else{
            $form->tplf = "changemisc.tpl";
        }
        $form->fe('text', "Title", "Title (Mr/Mrs/Mz/Dr/Prof.)");
        $form->fep("width=10");
        $form->fe('text', "FirstName", "First Name");
        $form->fep("width=30,required=true");
        $form->fe('text', "Initials", "Initials");
        $form->fep("width=5");
        $form->fe('text', "FamilyName","Family  Name", 30);
        $form->fep("width=30,required=true");
        $form->fe('textarea', "Address","Address");
        $form->fep("width=30,lines=5");
        $form->fe('text', "TownCity", "Town or City");
        $form->fep("width=20");
        $form->fe('text', "PostCode", "Post Code");
        $form->fep("width=10, maxLength=10, maxChar=10");
        $form->fe('text', "County", "County");
        $form->fep("width=30");
        if($creating){
            $form->fe("submit", "submit", "Next");
        }
        else{
            $form->fe("submit", "submit", "Save");
        }

        $return = '';
        if($form->processForm($_POST)){
            if(!$form->error){
                // update database
                $query = $form->querySave(true);
                $where = array("UserId"=>$this->id);
                $this->db->update($this->cfg->userMiscTable, $form->saveFields, $where, "SSP user admin: Saving misc member data");
                if($reDisplay){
                    $form->tda("saved");
                    $return = $form->create(true);
                }
                else{
                    $return = true;
                }
            }
            else{
                $return = $form->create(true);
            }
        }
        else{
            $query = $form->querySelect();
            $where = array("UserId"=>$this->id);
            $dataUpdate = $this->db->get($this->cfg->userMiscTable, $where, "SSP user admin: Getting User Misc data for update");
            $form->data = get_object_vars($dataUpdate);
            $return = $form->create(true);
        }
        return($return);
    }

    public function userChangeEmail($newEmail){
        // stub for user change email function, called by SSP_UserAdmin::changeEmail
    }

    public function userChangePassword($newPassword){
    	// user change password function
    	// called by SSP_UserAdmin::changePassword
    }

    public function userProg($data, $userId){
        // External system interfacing
        // returns true on external setup
        //
        // parameters
        //	$data - submit data from misc data form or other useful stuff
        //	$userId - users id, used for tracking
        return(true);
    }
}
