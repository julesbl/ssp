<!DOCTYPE html>
<html lang="{lang}">
<head>
	<!-- Testing form for data type testing -->
<!--
	Site by w34u
	http://www.w34u.com
	info@w34u.com
	 + 44 (0)1273 201344
	 + 44 (0)7833 512221
 -->
<title>Testing character error checking</title>
<meta name="Author" content="w34u - Julian Blundell" />
<meta name="resource-type" content="document" />
<meta name="Description" content="" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<h1>Testing character error checking</h1>
{:ne:setLanguage}
{setLanguage}
{:if:errorNumber}
<h2>Test results</h2>
<p>Error number: {errorNumber}<br />
Error: {errorString}</p>
{:endif:errorNumber}
{errorList}
<form action="{formAction}" method="post">
	{dataDesc} {data}<br />
	{dataTypeDesc} {dataType}<br />
	<input type="submit" value="Test" />
	{formHidden}
</form>
</body>
</html>