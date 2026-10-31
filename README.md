# ClubHub — TP4 PHP

## Lancer le projet

Ouvrez un terminal dans ce dossier et lancez le serveur PHP intégré :

```sh
php -S localhost:8000
```

Ouvrez ensuite `http://localhost:8000/`. Les pages disponibles sont `index.php`,
`evenements.php`, `evenement.php?id=1` et `bonjour.php`.

Le catalogue des événements se trouve dans `data/events.php`. Les fonctions
communes et les éléments partagés des pages se trouvent dans `includes/`.
L'inscription est traitée par `evenement.php` et les filtres de la liste
fonctionnent également sans JavaScript.
