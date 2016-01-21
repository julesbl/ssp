<?php 
namespace w34u\ssp;
require_once("../sspadmin/includeheader.php");
$session = new Protect();
/**
*   Site by w34u
*   http://www.w34u.com
*   info@w34u.com
*   +44 (0)1273 201344
*   +44 (0)7833 512221
*
*   Project:	SSP
*   Routine:	testDatabaseError.php
*   Created:	17/08/2009
*   Descrip:	Test the database error handling stuff.
*
*   Revision:	a
*   Rev. Date	17/08/2009
*   Descrip:	Created.
*/
$sql = "select someColumn from sometable";
$values = array();
$SSP_DB = \w34u\ssp\SspDb::getConnection();
$SSP_DB->query($sql, $values, "test quary for db routines");
?>