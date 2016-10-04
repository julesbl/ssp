<!DOCTYPE html>
<html lang="en">
<head>
<!--
	Site by w34u
	http://www.w34u.com
	info@w34u.com
	 + 44 (0)7833 512221
 -->
<title>{title}</title>
<meta name="Author" content="w34u - Julian Blundell" />
<meta name="resource-type" content="document" />
<meta name="Description" content="" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- <link rel="shortcut icon" href="/path/to/logo.ico" type="image/x-icon" /> -->
<link rel="stylesheet" href="/sspadmin/styles/styles.css" />
{:if:styles}
<style type="text/css">
{styles}
</style>
{:endif:styles}
</head>
<body>
	<div id="header">
		<h1><span>simple</span> <span class="bar">|</span> site protection</h1>
		<p><span>Simple Site Protection</span> | Project and Website Protection - Open Source Software</p>
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
		{:if:displayName}
		<p>User Name: {displayName}</p>
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
            <p>Simple Site Protection<br />Copyright 2005-2016 Julian Blundell - <a href="http://www.w34u.com" title="Go to w34u" target="_blank">w34u</a></p>
		</td>
		<td style="text-align:right;">
		<p>MIT Open Source License (MIT)</p>
		</td>
	</tr>
</table>
</div>
</body>
</html>