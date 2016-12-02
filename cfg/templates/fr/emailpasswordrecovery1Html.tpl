// html email used to recover passwords using the send password method 1, next line is email title
<h1>Notice d'un rétablissement d'un mot de passe sur le système de développement SSP</h1>
<p>Vous avez demandé pour un nouveau mot de passe ou pour rétablir l'ancien. Vous trouverez vos informations ci-dessous.</p>

{:if:UserName}
<p>Pseudonyme: {UserName}</p>
{:endif:UserName}

<p>Mot de passe: {UserPassword}</p>
