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
	<h1>Template examples: Example3</h1>
	<h2>{mainTitle}</h2>
	<p>{mainContent}</p>
	{:ifnot:displayOther} // line removed on execution
	<h4>Content displayed when variable does not exist</h4>
	<p><a href="?displayRest=1">Display other content</a></p>
	{:endif:displayOther} // line removed on execution
	{:if:displayOther} // line removed on execution
	<h4>Variable exists, now displayed</h4>
	<p>Content now displayed on variable exisiting</p>
	{:endif:displayOther} // line removed on execution
	</body>
</html>