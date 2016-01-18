<?php
namespace w34u\ssp;

require 'includeheader.php';

$session= new Protect("user");
$pageTitle="SSP - Lister Help";
$SSP_template = new Template($pageTitle, "sspgeneraltemplate.tpl");
$SSP_template->includeTill("menu");
echo '<a href="'.$SSP_Config->userLister.'">Back to Lister</a>';
$SSP_template->includeTill("content");
?>
<h1>Lister Help</h1>
<p>How to use the results lister.</p>
<h2>List Options </h2>
<p>To view information on an artist simply click on their name.</p>
<p>The alphabetical list at the top adds an additional filter on the family name of the member, example: if you click on &quot;c&quot; only artists who's family names start with &quot;c&quot; will be displayed, clicking on &quot;all&quot; displays the complete list again.</p>
<?php if($session->admin){?>
<p>Clicking on the button "New Members" will display the latest members who have joined, and replied to their emails but need to be checked by admin.</p>
<?php } ?>
<h2>Side Menu</h2>
<h3>My Details</h3>
<p>View and modify your own information. </p>
<h3>New Search</h3>
<p>Change the search criteria for the list, allows searches on various fields to find the members for which you are looking.</p>
<?php if($session->admin){?>
<h3>New User</h3>
<p>Creat new users, including admin users, without emails being sent anywhere, experienced users only.</p>
<h3>Email All</h3>
<p>Send an email message to all users in the current list. This allows you to email all members, all thos whose names begin with q etc.</p>
<h3>Export List</h3>
<p>Exports selected information on the current list to a CSV (comma seperated variable) file, this can be imported by many applications such as databases, spread sheets and word processing programs.</p>
<?php } ?>
<h3>Logoff</h3>
<p>Exit SSP lister and return to home page. </p>
<h3>Help</h3>
<p>This help section. </p>
<?php
$SSP_template->displayFooter();
?>

