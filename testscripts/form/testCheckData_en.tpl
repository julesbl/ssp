<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Testing form for data type testing -->
<!--
	Site by w34u
	http://www.w34u.com
	info@w34u.com
	 + 44 (0)1273 201344
	 + 44 (0)7833 512221
 -->
<title>Test form field data checking</title>
<meta name="Author" content="w34u - Julian Blundell" />
<meta name="resource-type" content="document" />
<meta name="Description" content="" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<style type="text/css">
	ul.SFCError{
		color: red;
		list-style: none;
		margin: 12px 0 0 0;
		padding: 0;
	}
	ul.SFCError li{
		margin-bottom: 5px;
	}
	input.SFCError{
		border: solid 2px red;
	}
	label.SFCError{
		font-weight: bold;
		color: red;
	}
</style>
<h1>Testing form field error checking</h1>
{:ne:setLanguage}
{setLanguage}
<p>Global error list</p>
{errorList}
{:if:localErrors}
<p>Form with local errors</p>
{:endif:localErrors}
<form method="{formMethod}" action="{formAction}">
	{textTypeErrorList}
	{textTypeDesc} {textType}<br />
	
	{passwordTypeErrorList}
	{passwordTypeDesc} {passwordType}<br />
	
	{domTypeErrorList}
	{domTypeDesc} {domType}<br />
	
	{emailTypeErrorList}
	{emailTypeDesc} {emailType}<br />
	{emailDotAtErrorList}
	{emailDotAtDesc} {emailDotAt}<br />
	{emailLengthErrorList}
	{emailLengthDesc} {emailLength}<br />
	
	{dateTypeErrorList}
	{dateTypeDesc} {dateType}<br />
	
	{timeTypeErrorList}
	{timeTypeDesc} {timeType}<br />
	
	{phoneTypeErrorList}
	{phoneTypeDesc} {phoneType}<br />
	
	{intTypeErrorList}
	{intTypeDesc} {intType}<br />
	
	{realTypeErrorList}
	{realTypeDesc} {realType}<br />
	
	{hexTypeErrorList}
	{hexTypeDesc} {hexType}<br />
	
	{octTypeErrorList}
	{octTypeDesc} {octType}<br />
	
	{binTypeErrorList}
	{binTypeDesc} {binType}<br />
	
	{textRequiredErrorList}
	{textRequiredDesc} {textRequired}<br />
	{textMinCharsErrorList}
	{textMinCharsDesc} {textMinChars}<br />
	{textMaxCharsErrorList}
	{textMaxCharsDesc} {textMaxChars}<br />
	<input type="submit" value="Test" />
{formHidden}
</form>