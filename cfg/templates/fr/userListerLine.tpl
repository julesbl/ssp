<tr>
	<td><p><a href="{memberAdminUrl}?userId={UserId}">{FirstName} {FamilyName}</a></p></td>
	<td><p>{TownCity}</p></td>
	<td><p>{UserStatus}</p></td>
	<td>
		{:ifnot:noDelete}
		<a href="{userListerUrl}?command=delete&amp;userId={UserId}"><img src="images/delicon.gif" alt="Supprimer utilisateur" title="Supprimer utilisateur" /></a>
		{:endif:noDelete}
		{:if:noDelete}
		&nbsp;
		{:endif:noDelete}
	</td>
</tr>