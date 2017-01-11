<!-- passwordrecover.tpl - template for routine to start password recovery -->
<h1>Rétablissement de mot de passe</h1>
<p>Veuillez composer votre mot de passe enregistré et un email pour rétablissement d'un mote de passe vous y sera envoyé.</p>
{errorList}
<form method="{formMethod}" action="{formAction}">
<table>
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
{:if:sent}
<p>L'envoi de l'email pour rétablir votre mot de passe effectué.</p>
{:endif:sent}
<p><a href="{loginPath}">Retourner à la page du login</a></p>