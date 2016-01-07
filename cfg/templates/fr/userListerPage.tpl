<!-- userListerPage.tpl - template used to list users -->
<h1>Distributeur utilisateur SSP</h1>
{:ne:alphFilter}
{:ne:pageNav}
{alphFilter}{pageNav}<br clear="all" />
<table id="userList" cellpadding="0" cellspacing="0">
<tr>
	<th><p>Prénom et nom</p></th>
	<th><p>Ville</p></th>
	<th><p>Statut</p></th>
	<th><p>Fonction</p></th>
</tr>
{:ne:list}
{list}
</table>
{alphFilter}{pageNav}