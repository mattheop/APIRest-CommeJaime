
# API REST
API réalisé en cours d'architecture logicielle, elle permet de gérer les posts et les likes

# Fonctionnalités
Cette API prend les fonctionnalités suivantes
* Connexion
* Recuperation des posts
* Ajout de post
* Ajout de like
* Suppression de post
* Suppression de like
* Modification de post
* Gestion des roles (publisher, moderator)
* Affiche du nombre de like si publisher
* Affiche du nombre de like et des noms des utilisateurs qui ont liké/disliké si moderator


# Installation
L'installation suivante se fait grace a docker.
Vous pouvez néanmoins lancer l'API sans docker avec un serveur web de votre choix
et en modifiant les variables d'environnement (cf docker-compose).
N'oubliez pas de faire un composer install pour installer les dépendances.
Et d'utiliser les scripts sql pour créer la base de donnée (/docker/mysql/sql).
## Prérequis
* Docker
* Docker-compose
## Lancement pour un environnement de développement
```bash 
cd docker
docker-compose up -d
```
Dans cet environnement de développement, les ports sont ouverts et le service web est accessible sur le **port 8181**.
Un phpmyadmin est accessible sur le **port 8182**.

Le volume du serveur web est monté directement par rapport à la machine hôte pour permettre de voir les changements en temps réel sans rebuild.

Les erreurs sont affichées reportées sur la page.

## Lancement pour un environnement de production
```bash
cd docker
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```
_Nb: Vous pouvez rajouter un fichier docker-compose pour lancer des services supplémentaires comme un reverse proxy ou bien ouvrir les ports du service chatpot_web._

# Mettre à jour le serveur de production
Vous pouvez utiliser git pour mettre à jour les sources...
```bash
git pull
docker-compose -f docker-compose.yml -f docker-compose.prod.yml down -v
docker-compose -f docker-compose.yml -f docker-compose.prod.yml build
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```
