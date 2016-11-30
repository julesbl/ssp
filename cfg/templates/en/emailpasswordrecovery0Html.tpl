// html email used to recover passwords using the link method 0, next line is email title
<h1>SSP Development System password recovery email.</h1>
<p>You have requested a password recovery operation, click on the link below to enter a new password.</p>
<p>You have to do this fairly quickly or the recovery link will become invalid.</p>

{:if:UserName}
<p>User name: {UserName}</p>
{:endif:UserName}

<p><a href="{link}/{token}">{link}/{token}</a></p>
