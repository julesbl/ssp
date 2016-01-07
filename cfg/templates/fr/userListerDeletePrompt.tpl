<!-- userListerDeletePrompt.tpl - prompt on admin deleting user -->
<h1>Suppression de l'utilisateur en cours</h1>
<table>
	<tr><td>{Title} {FirstName} {Initials} {FamilyName}</td></tr>
	<tr><td>{Address}<br />{TownCity}<br />{County} {PostCode}<br />{Country}</td></tr>
</table>
<p>Êtes-vous sur de vouloir supprimer cet utilisateur ?</p>
<form action="{path}" method="post">
	<input type="submit" name="deleteUser" value="Supprimer utilisateur" /> <input type="submit" name="preserveUser" value="Keep user" />
	<input type="hidden" name="command" value="{command}" />
	<input type="hidden" name="userId" value="{UserId}" />
</form>