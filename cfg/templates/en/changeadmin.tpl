<!-- changeadmin.tpl - template for the change user login configuration function -->
<h1>Change user information</h1>
{errorList}
{:if:saved}
<p class="response">User admin data updated</p>
{:endif:saved}
<form method="{formMethod}" action="{formAction}">
	<table>
		<tr>
			<td>{UserIpCheckDesc}</td>
			<td>{UserIpCheck}</td>
		</tr>
		<tr>
			<td>{UserIpDesc}</td>
			<td>{UserIp}</td>
		</tr>
		<tr>
			<td>{use_two_factor_authDesc}</td>
			<td>{use_two_factor_auth}</td>
		</tr>
		<tr>
			<td>{UserAccessDesc}</td>
			<td>{UserAccess}</td>
		</tr>
		<tr>
			<td>{UserDisabledDesc}</td>
			<td>{UserDisabled}</td>
		</tr>
		<tr>
			<td>{UserPendingDesc}</td>
			<td>{UserPending}</td>
		</tr>
		<tr>
			<td>{UserAdminPendingDesc}</td>
			<td>{UserAdminPending}</td>
		</tr>
		<tr>
			<td>{CreationFinishedDesc}</td>
			<td>{CreationFinished}</td>
		</tr>
		<tr>
			<td>{UserWaitingDesc}</td>
			<td>{UserWaiting}</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Save" /></td>
		</tr>
	</table>
	{formHidden}
</form>