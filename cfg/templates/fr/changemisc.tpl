<!-- changemisc.tpl - template for the change miscellaneous info function -->
<h1>Changement d'informations d'utilisateur</h1>
{errorList}
{:if:saved}
<p class="response">Vos informations ont été sauvegardées</p>
{:endif:saved}
<form method="{formMethod}" action="{formAction}">
	<table>
		<tr>
			<td>{TitleDesc}</td>
			<td>{Title}</td>
		</tr>
		<tr>
			<td>{FirstNameDesc}</td>
			<td>
				{FirstNameErrorList}
				{FirstName}
			</td>
		</tr>
		<tr>
			<td>{InitialsDesc}</td>
			<td>{Initials}</td>
		</tr>
		<tr>
			<td>{FamilyNameDesc}</td>
			<td>{FamilyName}</td>
		</tr>
		<tr>
			<td>{AddressDesc}</td>
			<td>{Address}</td>
		</tr>
		<tr>
			<td>{TownCityDesc}</td>
			<td>{TownCity}</td>
		</tr>
		<tr>
			<td>{PostCodeDesc}</td>
			<td>{PostCode}</td>
		</tr>
		<tr>
			<td>{CountyDesc}</td>
			<td>{County}</td>
		</tr>
		<tr>
			<td></td>
			<td>{submit}</td>
		</tr>
	</table>
	{formHidden}
</form>