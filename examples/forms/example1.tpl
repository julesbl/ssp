<!-- example1.tpl -->
<!-- display misc data added to the form -->
<p>{miscTplData}</p>
<!-- Display the error list if form submitted with errors -->
{errorList}
<form action="{formAction}" method="{formMethod}">
	<!-- first element label followed by the element -->
	{firstElementDesc} {firstElement}<br />
	{paswordDesc} {pasword}<br />
	{radio_test_0} {radio_test_1}<br />
	{radio_test_2_0} {radio_test_2_1}<br />
	{radio_test_3_0} {radio_test_3_1}<br />
	<input type="submit" value="The go button" />
	<!-- hidden form fields go here -->
	{formHidden}
</form>