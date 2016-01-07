<!-- logonpage.tpl - Template for login page -->
<h1>Please Log on</h1>
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
				<p>Ticking this checkbox wiil place a cookie on your machine in the browser storage, it will only be readable by this site.</p>
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
<p id="passwordRecovery">Have you forgotten your password?, <a href="{passwordRecoveryLink}">go here....</a>
<br /> or <a href="{joinSiteLink}">Join {siteName}</a>
<br /> or return to the <a href="{siteHome}">homepage</a></p>