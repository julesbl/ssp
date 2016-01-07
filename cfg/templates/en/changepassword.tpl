<!-- changepassword.tpl - template for routine to change the user password -->
<h1>Change Password</h1>
<p>Please enter your
{:if:oldPasswordDesc}
 current password and
{:endif:oldPasswordDesc}
 new password twice.
</p>
{errorList}
{:if:saved}
<p class="response">Your password has been changed</p>
{:endif:saved}
<form method="{formMethod}" action="{formAction}">
	<table>
		{:if:oldPasswordDesc}
		<tr>
			<td>{oldPasswordDesc}</td>
			<td>{oldPassword}</td>
		</tr>
		{:endif:oldPasswordDesc}
		<tr>
			<td>{passwordDesc}</td>
			<td>{password}</td>
		</tr>
		<tr>
			<td>{password2Desc}</td>
			<td>{password2}</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Save new password" /></td>
		</tr>
	</table>
	{formHidden}
</form>