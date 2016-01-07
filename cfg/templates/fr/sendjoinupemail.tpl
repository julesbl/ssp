<!-- sendjoinupemail.tpl - template for the change miscellaneous info function -->
<h1>Envoyer invitation d'abonnement</h1>
{errorList}
{:if:saved}
<p class="response">Invitation d'abonnement envoyée</p>
{:endif:saved}
<form method="{formMethod}" action="{formAction}">
	{submit}
	{formHidden}
</form>