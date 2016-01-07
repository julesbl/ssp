<!-- changeemail.tpl - template for the change email function -->
<h1>Change Email</h1>
<p>Please enter your
{:if:passwordDesc}
 password and
{:endif:passwordDesc}
 new email address</p>
{errorList}
{:if:saved}
<p class="response">Your email has been changed</p>
{:endif:saved}
<form method="{formMethod}" action="{formAction}">
	<table>
		{:if:passwordDesc}
		<tr>
			<td>{passwordDesc}</td>
			<td>{password}</td>
		</tr>
		{:endif:passwordDesc}
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