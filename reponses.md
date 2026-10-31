# Défi PHP

## Désactiver JavaScript et envoyer un formulaire vide

La requête arrive quand même à `evenement.php`. Le serveur refuse l’inscription, réaffiche le formulaire et indique les erreurs sous le nom, l’adresse e-mail, le niveau et l’acceptation du règlement. La validation ne dépend donc pas du JavaScript.

## Envoyer une requête POST avec curl

La requête `curl -X POST -d "name=A&email=faux" "http://localhost:8000/evenement.php?id=1"` est également refusée par PHP : le nom est trop court, l’adresse e-mail est invalide, le niveau manque et le règlement n’a pas été accepté.
