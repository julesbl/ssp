<!-- exampl1.tpl.tpl - demo template -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Untitled Document - w34u</title>
	<meta name="Description" content="template description" />
	<link href="css/styles.css" rel="stylesheet" type="text/css" />
	<!--
		Site by w34u
		Brighton Media Centre
		Suit G03
		15-17 Middle Street
		Brighton
		East Sussex BN1 1AL
		United Kingdom
		t. +44 (0)1273 321143
		m. +44 (0)7833 512221
		e. info@w34u.com
		w. www.w34u.com
	-->
	</head>
	<body>
	<h1>Template examples: Example1</h1>
	<h2>Simple data replacement</h2>
	<h3>{Example1}</h3>
	<p>{someText}</p>
	<p>Escaped html text<br />
		{escapedHtml}</p>
	<h4>Not escaped, designated by the object method</h4>
	{programNe}
	<h4>Not escaped, designated by the template</h4>
	{:ne:templateNe} // this line removed on execution
	{templateNe}
	<h4>Text with newlines no nl2br</h4>
	<p>{textWithBreaksNoNl}</p>
	<h4>Text with newlines with nl2br</h4>
	{:nl2br:textWithBreaksNl2Br} // this line removed on execution
	<p>{textWithBreaksNl2Br}</p>
	<h4>Some tag with no data supplied, is just shown</h4>
	<p>{someTag}</p>
	</body>
</html>