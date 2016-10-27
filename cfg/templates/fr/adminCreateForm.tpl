<!-- adminCreateForm.tpl - form used in first admin creation -->
<form action="{formAction}" method="{formMethod}">
	<table>
	{errorList}
	{:if:email}
	<tr><td>{emailDesc}</td><td>{email}</td></tr>
	{:endif:email}
	{:if:userName}
	<tr><td>{userNameDesc}</td><td>{userName}</td></tr>
	{:endif:userName}
	<tr><td>{password1Desc}</td><td>{password1}</td></tr>
	<tr><td>{password2Desc}</td><td>{password2}</td></tr>
	<tr><td></td><td><input type="submit" value="Create Admin" /></td></tr>
	</table>
	{formHidden}
</form>