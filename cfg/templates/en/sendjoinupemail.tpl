<!-- sendjoinupemail.tpl - template for the change miscellaneous info function -->
<h1>Send join up email to user</h1>
{errorList}
{:if:saved}
<p class="response">Joinup email sent</p>
{:endif:saved}
<form method="{formMethod}" action="{formAction}">
	{submit}
	{formHidden}
</form>