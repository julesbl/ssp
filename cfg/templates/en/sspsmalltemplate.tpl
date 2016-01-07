<!DOCTYPE html>
<html lang="en">
<head>
<!--
Site by w34u
http://www.w34u.com
info@w34u.com
 + 44 (0)1273 201344
 + 44 (0)7833 512221
 
 Small template used for login screens and other stand alone programs.
 -->
<title>{title}</title>
<meta name="Author" content="w34u - Julian Blundell" />
<meta name="resource-type" content="document" />
<meta name="Description" content="" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="styles/styles.css" />
{:if:styles}
<style type="text/css">
{styles}
</style>
{:endif:styles}
</head>
<body id="smallTemplate">
    <div id="header">
        <h1><span>simple</span> <span class="bar">|</span> site protection</h1>
        <p><span>Simple Site Protection</span> | Project and Website Protection - Open Source Software</p>
		{:if:languageSelectForm}
		{:ne:languageSelectForm}
		{languageSelectForm}
		{:endif:languageSelectForm}
    </div>
    <table id="body">
        <td id="content">
			{:ne:content}
            {content}
        </td>
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