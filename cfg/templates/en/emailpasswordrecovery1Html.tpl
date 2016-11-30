// html email used to recover passwords using the send password method 1, next line is email title
<h1>SSP Development System password recovery email.</h1>
<p>You have requested a password recovery operation, find your login information below.</p>

{:if:UserName}
<p>User name: {UserName}</p>
{:endif:UserName}

<p>Password: {UserPassword}</p>
