<!-- Testing form for data type testing setting the language -->
{errorList}
<form action="{formAction}" method="post">
	{languageDesc} {language} <br />
	{localErrorDesc} {localError} <br />
	<input type="submit" value="Set language and error list to use" />
	{formHidden}
</form>