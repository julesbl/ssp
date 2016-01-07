<!-- displayAdminInfo.tpl - template for the advanced user info display -->
<table>
	<tr>
		<td>Id</td><td>{UserId}</td>
	</tr>
	<tr>
		<td>Email</td><td>{UserEmail}</td>
	</tr>
	<tr>
		<td>User login name</td><td>{UserName}</td>
	</tr>
	<tr>
		<td>User access</td><td>{userAccess}</td>
	</tr>
	<tr>
		<td>Fixed IP</td><td>{UserIp}</td>
	</tr>
		{:if:ipCheckEnabled}
	<tr>
		<td>IP Check</td><td>This is enabled for this user</td>
	</tr>
		{:endif:ipCheckEnabled}
		{:if:ipCheckDisabled}
	<tr>
		<td>IP Check</td><td>This is not enabled for this user</td>
	</tr>
		{:endif:ipCheckDisabled}
	<tr>
		<td>Date user last logged on</td><td>{UserDateLogon}</td>
	</tr>
	<tr>
		<td>Previous to that</td><td>{UserDateLastLogon}</td>
	</tr>
	<tr>
		<td>Date user created</td><td>{UserDateCreated}</td>
	</tr>
		{:if:userDisabled}
	<tr>
		<td>User disable</td><td class="SSP_Error">This user has been disabled!!!!</td>
	</tr>
		{:endif:userDisabled}
		{:ifnot:userDisabled}
	<tr>
		<td>User disable</td><td>User enabled</td>
	</tr>
		{:endif:userDisabled}

		{:if:userPending}
	<tr>
		<td>Program pending</td><td class="SSP_Error">Waiting additional program action</td>
	</tr>
		{:endif:userPending}
		{:ifnot:userPending}
	<tr>
		<td>Program pending</td><td>Additional program finished</td>
	</tr>
		{:endif:userPending}

		{:if:userAdminPending}
	<tr>
		<td>Admin pending</td><td class="SSP_Error">Waiting admin action</td>
	</tr>
		{:endif:userAdminPending}
		{:ifnot:userAdminPending}
	<tr>
		<td>Admin pending</td><td>Admin acted</td>
	</tr>
		{:endif:userAdminPending}

		{:ifnot:creationFinished}
	<tr>
		<td>Creation</td><td class="SSP_Error">User not finished being created!!!</td>
	</tr>
		{:endif:creationFinished}
		{:if:creationFinished}
	<tr>
		<td>Creation</td><td>User created</td>
	</tr>
		{:endif:creationFinished}

		{:if:userWaiting}
	<tr>
		<td>User reply</td><td class="SSP_Error">Waiting for user to act on email!!</td>
	</tr>
		{:endif:userWaiting}
		{:ifnot:userWaiting}
	<tr>
		<td>User reply</td><td>User acted on email</td>
	</tr>
		{:endif:userWaiting}
</table>