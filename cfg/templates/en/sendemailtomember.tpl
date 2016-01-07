<!-- sendemailtomember.tpl - template request text for email -->
<h1>Send email to a member</h1>
{errorList}
{:if:saved}
<p class="response">Email sent to member</p>
{:endif:saved}
<form method="{formMethod}" action="{formAction}">
	<table>
	<tr>
		<td>{subjectDesc}</td>
		<td>{subject}</td>
	</tr>
	<tr>
		<td>{messageDesc}</td>
		<td>{message}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>{submit}</td>
	</tr>
	</table>
	{formHidden}
</form>
