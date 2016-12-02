// main html template for SSP emails
<html lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>{subject}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	</head>
	<body style="margin: 0; padding: 0;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
{:ne:content}
{content}
				</td>
			</tr>
			<tr>
				<td style="border-top: 1px solid #ccc; margin-top: 7px;">
					<p>Email envoyé à partir de {domain}.<br />
					Veuillez contacter le support à {adminEmail} si vous auriez des questions.</p>
				</td>
			</tr>
		</table>
	</body>
</html>
