<tr class="oddLine">
	<td><p><a href="{memberAdminUrl}/info/{UserId}">{FirstName} {FamilyName}</a></p></td>
	<td><p>{TownCity}</p></td>
	<td><p>{UserStatus}</p></td>
	<td>
		{:ifnot:noDelete}
		<a href="{userListerUrl}/delete/{UserId}"><img src="images/delicon.gif" alt="delete user" title="delete user" /></a>
		{:endif:noDelete}
		{:if:noDelete}
		&nbsp;
		{:endif:noDelete}
	</td>
</tr>