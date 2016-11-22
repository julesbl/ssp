<!-- displayAdminInfo.tpl - template for the advanced user info display -->
<table>
	<tr>
		<td>Id</td><td>{UserId}</td>
	</tr>
	<tr>
		<td>Email</td><td>{UserEmail}</td>
	</tr>
	<tr>
		<td>Pseudonyme</td><td>{UserName}</td>
	</tr>
	<tr>
		<td>Accès aux utilisateurs</td><td>{userAccess}</td>
	</tr>
	<tr>
		<td>IP fixe</td><td>{UserIp}</td>
	</tr>
		{:if:ipCheckEnabled}
	<tr>
		<td>Vérification IP</td><td>Permis pour cet utilisateur</td>
	</tr>
		{:endif:ipCheckEnabled}
		{:if:ipCheckDisabled}
	<tr>
		<td>Vérification IP</td><td>Non permis pour cet utilisateur</td>
	</tr>
		{:endif:ipCheckDisabled}
	<tr>
		<td>Dernière fois utilisé</td><td>{UserDateLogon}</td>
	</tr>
	<tr>
		<td>Auparavant</td><td>{UserDateLastLogon}</td>
	</tr>
	<tr>
		<td>Date de la création de l'utilisateur</td><td>{UserDateCreated}</td>
	</tr>
		{:if:Use_two_factor_auth}
	<tr>
		<td>Authentification à deux facteurs</td><td>Activée</td>
	</tr>
		{:endif:Use_two_factor_auth}
		{:ifnot:Use_two_factor_auth}
	<tr>
		<td>Authentification à deux facteurs</td><td>Non utilisé</td>
	</tr>
		{:endif:Use_two_factor_auth}
		{:if:userDisabled}
	<tr>
		<td>Utilisateur désactivé</td><td class="SSP_Error">Utilisateur désactivé !!!!</td>
	</tr>
		{:endif:userDisabled}
		{:ifnot:userDisabled}
	<tr>
		<td>Désactiver l'utilisateur</td><td>Utilisateur activé</td>
	</tr>
		{:endif:userDisabled}

		{:if:userPending}
	<tr>
		<td>Logiciel en cours d'attente</td><td class="SSP_Error">Approbation d'autres actions du logiciel en cours d'attente</td>
	</tr>
		{:endif:userPending}
		{:ifnot:userPending}
	<tr>
		<td>Logiciel en cours d'attente</td><td>Programmation terminée</td>
	</tr>
		{:endif:userPending}

		{:if:userAdminPending}
	<tr>
		<td>Administration en cours d'attente</td><td class="SSP_Error">Action d'administrateur en cours</td>
	</tr>
		{:endif:userAdminPending}
		{:ifnot:userAdminPending}
	<tr>
		<td>Administration en cours d'attente</td><td>Administration terminée</td>
	</tr>
		{:endif:userAdminPending}

		{:ifnot:creationFinished}
	<tr>
		<td>Création</td><td class="SSP_Error">Utilisateur pas encore créé !!!</td>
	</tr>
		{:endif:creationFinished}
		{:if:creationFinished}
	<tr>
		<td>Création</td><td>Utilisateur créé</td>
	</tr>
		{:endif:creationFinished}

		{:if:userWaiting}
	<tr>
		<td>User reply</td><td class="SSP_Error">Attendant que l'utilisateur suive les étapes dans le mail!!</td>
	</tr>
		{:endif:userWaiting}
		{:ifnot:userWaiting}
	<tr>
		<td>Réponse utilisateur</td><td>Étapes suivies avèc succès</td>
	</tr>
		{:endif:userWaiting}
</table>