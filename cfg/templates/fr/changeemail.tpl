<!-- changeemail.tpl - template for the change email function -->
<h1>Changement d'adresse email</h1>
<p>Veuillez saisir 
{:if:passwordDesc}
 un mot de passe et
{:endif:passwordDesc}
 une nouvelle adresse email</p>
{errorList}
{:if:saved}
<p class="response">Adresse email changée</p>
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