<!-- changepassword.tpl - template for routine to change the user password -->
<h1>Changer mot de passe</h1>
<p>Veuillez entrer votre 
{:if:oldPasswordDesc}
 mot de passe actuel et
{:endif:oldPasswordDesc}
 le nouveau mot de passe deux fois
</p>
{errorList}
{:if:saved}
<p class="response">Nouveau mot de passe validé</p>
{:endif:saved}
<form method="{formMethod}" action="{formAction}">
	<table>
		{:if:oldPasswordDesc}
		<tr>
			<td>{oldPasswordDesc}</td>
			<td>{oldPassword}</td>
		</tr>
		{:endif:oldPasswordDesc}
		<tr>
			<td>{passwordDesc}</td>
			<td>{password}</td>
		</tr>
		<tr>
			<td>{password2Desc}</td>
			<td>{password2}</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Valider nouveau mot de passe" /></td>
		</tr>
	</table>
	{formHidden}
</form>