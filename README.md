# LemonSymfony

## Description

Ceci est un simple projet de gestion d'évènements responsive utilisant le framework Symfony.
Il utilise l'ORM Doctrine, la librairie FakerPHP et le templating par TWIG.
Il utilise la librairie Bootstrap pour ce qui est du CSS.

## Installation

1. Cloner le projet : `git clone https://github.com/FskiTeo/LemonSymfony.git`
2. Copier le fichier `.env.exemple` vers `.env`
3. Configurer la base de données dans le fichier `.env` avec la variable `DATABASE_URL`
4. Installer les dépendances via `composer
5. Effectuer les migrations de la base de données: `php bin/console doctrine:migration:migrate`
6. Ajouter des fixtures à la BDD : `php bin/console doctrine:fixtures:load`
7. Exectuer `npm install` puis `npm run build` afin de build la dépendance Bootstrap
8. Lancer le serveur : `php -S localhost:8000 -t public/` ou `symfony server:start`

## Mode production
1. Executer `composer dump-env prod` afin de créer un fichier .env.local.php optimisé
2. Executer `composer install --no-dev --optimize-autoloader` afin d'installer les dépendances nécessaires (et de désinstaller les dépendances de developpement)
3. Lancer le serveur : `php -S localhost:8000 -t public/` ou `symfony server:start`

## Utilisation
### Routes :
Pages Disponibles sans authentification:
- `/` : Page d'accueil avec l'affichage des évènements
- `/login` : Page de connexion
- `/register` : Page d'inscription

Pages disponibles après authentification:
- `/mes-inscriptions` : Page listant les évènements auxquels l'utilisateur est inscrit
- `/create` : Page de création d'évènement
- `/logout` : Déconnexion de l'utilisateur

Pages disponibles pour l'administrateur:
- `/lieux` : Page permettant de supprimer des lieux

### Authentification :
L'authentification se fait via le formulaire de connexion.
Vous pouvez vous connecter avec les identifiants suivants:
```
email : à retrouver dans votre base de données (généré aléatoirement)
password : LemonLille59*
```
Pour des raisons de simplicité, tous les utilisateurs créés par les fixtures possèdent le même mot de passe.