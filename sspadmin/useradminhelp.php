<?php
require("includeheader.php");
$session= new Protect("user");
$pageTitle="SSP - User Admin Help";
$SSP_template = new Template($pageTitle, "sspgeneraltemplate.tpl");
$SSP_template->includeTill("menu");
echo '<a href="'.$SSP_Config->userAdminScript.'">Back to Lister</a>';
$SSP_template->includeTill("content");
?>
<h1>User Details Help </h1>
<p>Functions available in the user details screen.</p>
<p>The entries marked by * are only available in your own details.</p>
<h3>Member Info</h3>
<p>Member information, only displays the enabled  and defualt sections.</p>
<?php if($session->admin){?>
<h3>Advanced Info</h3>
<p>Displays advanced information on the user, such things as their email, login level, when they last logged in and when they joined.</p>
<?php } ?>
<h3> * Change Password</h3>
<p>Change your password. </p>
<h3> * Change Email</h3>
<p>Change your email. </p>
<h3> * Change Common Info</h3>
<p>Change general information. </p>
<?php if($session->admin){?>
<h3>Change Advanced</h3>
<p>Change user login abilities, for instance you can disable a member.</p>
<h3>Joining Email</h3>
<p>If an email has become lost or you have created a new member you can if you set the "Waiting for user to act on email" clicking on this option send a standard joining email to this user.</p>
<?php } ?>
<h3> Email User</h3>
<p>Email the selected user. </p>
<h3> Return to List </h3>
<p>Back to the current search list. </p>
<h3>Help</h3>
<p>This help section. </p>
<?php if($session->admin){?>
<h2>Explanation of Advanced properties</h2>
<p><strong>User Access Rights</strong> - Login level of member</p>
<p><strong>User Disabled</strong> - Member is disabled, cannot log in or be viewed by other users</p>
<p><strong>User Pending Program enable</strong> - Member waiting for program function to finish, not used in this application</p>
<p><strong>User waiting admin vetting</strong> - Member has not been viewed and ok'd by an administrator, cannot login or be viewed by other members</p>
<p><strong>User creation finished</strong> - Member has succesfully filled out all the forms in the joining process.</p>
<p><strong>Waiting for user to act on email</strong> - Waiting for user to click on link in joining email, cannot login or be viewed by other members.</p>
<p>For a Member to be properly active the only flag that should be true is "User creation finished".</p>
<?php } ?>
<?php
$SSP_template->displayFooter();
?>

