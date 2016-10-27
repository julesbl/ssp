<!-- adminCreate.tpl - used in creation of the database and intial login  -->
<h1>Creating or updating the database</h1>
{:if:database_creation}
<h2>Ruckus database version control output</h2>
<pre>
	{database_creation}
</pre>
{:endif:database_creation}
<h2>Admin users creation</h2>
{:if:admin_creation_status}
<p>{admin_creation_status}</p>
{:endif:admin_creation_status}
{:if:form}
<p>Please enter details for the first administrator</p>
{:ne:form}
{form}
{:endif:form}
<p><a href="{adminPath}">Go to admin</a></p>