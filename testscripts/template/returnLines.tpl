<!-- return these two lines -->
<p>just soem text</p>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!--
Site by w34u
http://www.w34u.com
info@w34u.com
 + 44 (0)1273 201344
 + 44 (0)7833 512221
 -->
<title>{title}</title>

</head>

<body>
<h1>{title}</h1>
<p>Testing the multiple replaces with conditional{firstMultiple}</p>
{include1}
<p>After include1 {secondMultiple}</p>
{include2}
{:if:thirdMultiple }
<p>After include2 {thirdMultiple}, {forthMultiple}</p>
{:endif: thirdMultiple}
<p>Multiple example finished</p>
{blank}{firstMultiple}
</body>
</html>