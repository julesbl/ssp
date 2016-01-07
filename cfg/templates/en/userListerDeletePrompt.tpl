<!-- userListerDeletePrompt.tpl - prompt on admin deleting user -->
<h1>Deleting user</h1>
<table>
	<tr><td>{Title} {FirstName} {Initials} {FamilyName}</td></tr>
	<tr><td>{Address}<br />{TownCity}<br />{County} {PostCode}<br />{Country}</td></tr>
</table>
<p>Are you sure you wish to delete this user?</p>
<form action="{path}" method="post">
	<input type="submit" name="deleteUser" value="Delete User" /> <input type="submit" name="preserveUser" value="Keep user" />
	<input type="hidden" name="command" value="{command}" />
	<input type="hidden" name="userId" value="{UserId}" />
</form>