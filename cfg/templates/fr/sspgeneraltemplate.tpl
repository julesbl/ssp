<!DOCTYPE html>
<html lang="fr">
<head>
<!--
	Site by w34u
	http://www.w34u.com
	info@w34u.com
	 + 44 (0)1273 201344
	 + 44 (0)7833 512221
 -->
<title>{title}</title>
<meta name="Author" content="w34u - Julian Blundell" />
<meta name="resource-type" content="document" />
<meta name="Description" content="" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- <link rel="shortcut icon" href="/path/to/logo.ico" type="image/x-icon" /> -->
<link rel="stylesheet" type="text/css" href="/sspadmin/styles/styles.css" />
{:if:styles}
<style type="text/css">
{styles}
</style>
{:endif:styles}
</head>
<body>
	<div id="header">
		<h1><span>simple</span> <span class="bar">|</span>protection du site</h1>
	     <p><span>Simple Site Protection</span> | Protection du site simple - Logiciel Open Source</p>
	{:if:languageSelectForm}
		{:ne:languageSelectForm}
		{languageSelectForm}
		{:endif:languageSelectForm}
	</div>
{:if:mainMenu}
{:ne:mainMenu}
<div id="mainMenu">
    {mainMenu}
</div>
{:endif:mainMenu}
<table id="body">
	<tr>
		<td id="menu">
        <div id="pageHeight"></div>
		{:ne:menu}
		{menu}
		</td>
		<td id="content">
	{:if:showDisableSetupText}
	<h2 style="color: red;">Setup is still enabled, please disable by setting $enableSetup = false in configuration</h2>
	{:endif:showDisableSetupText}
		{:if:displayName}
		<p>Pseudonyme: {displayName}</p>
		{:endif:displayName}
		{:ne:content}
		{content}
		</td>
	</tr>
</table>
<div id="footerBackground">
<table id="footer" border="0" cellpadding="0" align="center">
	<tr>
		<td style="text-align:left;">
            <p>Simple Site Protection<br />Copyright 2005-2012 Julian Blundell - <a href="http://www.w34u.com" title="Go to w34u" target="_blank">w34u</a></p>
		</td>
		<td style="text-align:right;">
		<p>Common Development and Distribution<br />License (CDDL)</p>
		</td>
	</tr>
</table>
</div>
</body>
</html>