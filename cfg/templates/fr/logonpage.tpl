<!-- logonpage.tpl - Template for login page -->
<h1>Veuillez vous connecter</h1>
<form method="{formMethod}" action="{formAction}" id="adminSmallForm">
{errorList}
	{:if:error}
	<p>{error}</p>
	{:endif:error}
	<table>
		{:if:email}
		<tr>
			<td>
				{emailDesc}
			</td>
			<td>
				{email}
			</td>
		</tr>
		{:endif:email}
		{:if:user}
		<tr>
			<td>
				{userDesc}
			</td>
			<td>
				{user}
			</td>
		</tr>
		{:endif:user}
		{:if:emaillogin}
		<tr>
			<td colspan="2">
				{emaillogin}<br />
				<p>An email will be sent to your account with a link that will log you in</p>
			</td>
		</tr>
		{:endif:emaillogin}
		<tr>
			<td>
				{passwordDesc}
			</td>
			<td>
				{password}
			</td>
		</tr>
		{:if:rememberMe}
		<tr>
			<td colspan="2">
				{rememberMe}<br />
				<p>En cliquant cette boîte, vous comprenez qu'un témoin sera mis dans le moteur de recherche de votre machine qui ne sera lisable que par ce site.</p>
			</td>
		</tr>
		{:endif:rememberMe}
		<tr>
			<td>
			</td>
			<td>
				<input type="submit" value="Login" name="login" id="logonButton" style="float:right" />
			</td>
		</tr>
	</table>
		{formHidden}
</form>
<p id="passwordRecovery">Mot de passe oublié?, <a href="{passwordRecoveryLink}">veuillez cliquer ici....</a>
<br /> ou <a href="{joinSiteLink}">S'abonner {siteName}</a>
<br /> ou retourner à la <a href="{siteHome}">Page d'acceuil</a></p>