<!-- passwordrecover.tpl - template for routine to start password recovery -->
<h1>Password Recovery</h1>
<p>Please enter your registered email and a recovery email will be sent to that address.</p>
{errorList}
<form method="{formMethod}" action="{formAction}">
<table>
<tr>
	<td>{emailDesc}</td>
	<td>{email}</td>
</tr>
<tr>
	<td></td>
	<td>{submit}</td>
</tr>
</table>
{formHidden}
</form>
{:if:sent}
<p>The if your email is listed the password recovery email has been sent to your email address</p>
{:endif:sent}
<p><a href="{loginPath}">Return to login screen</a></p>