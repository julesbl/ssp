<!-- exampl1.tpl.tpl - demo template -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>{title}</title>
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
	<h1>Template examples: Example2</h1>
	<h2>Sub templates of various types</h2>
	<h3>{mainTitle}</h3>
	<p>{mainContent}</p>
	<h4>Template inserted with its own data in the data</h4>
	<div style="border: 1px #000 solid">{subTemplate}</div>
	<h4>Inserted from the data, using the main data</h4>
	<div style="border: 1px #aaa solid">{:includeti:subTemplateMainData}</div>
	<h4>Template included no data processes</h4>
	{:include:example2Sub2.tpl}
	<h4>Included template file and process using main data</h4>
	{:includet:example2Sub3.tpl}
	</body>
</html>