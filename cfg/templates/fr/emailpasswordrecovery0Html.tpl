// html email used to recover passwords using the link method 0, next line is email title
<h1>Email confirmant le rétablissement d'un mot de passe du système de développement SSP</h1>
<p>Vous avez demandé à faire rétablir un mot de passe. Veuillez cliquer le lien en bas pour saisir un nouveau mot de passe.</p>
<p>Il est important que vous suiviez les étapes du mail aussitôt que possible avant que le lien ne s'expire.</p>

{:if:UserName}
<p>Pseudonyme: {UserName}</p>
{:endif:UserName}

<p><a href="{link}/{token}">{link}/{token}</a></p>
