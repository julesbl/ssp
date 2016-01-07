<!-- userListerPage.tpl - template used to list users -->
<h1>SSP User Lister</h1>
{:ne:alphFilter}
{:ne:pageNav}
{alphFilter}{pageNav}<br clear="all" />
<table id="userList" cellpadding="0" cellspacing="0">
<tr>
	<th><p>Name</p></th>
	<th><p>City or Town</p></th>
	<th><p>Status</p></th>
	<th><p>Function</p></th>
</tr>
{:ne:list}
{list}
</table>
{alphFilter}{pageNav}