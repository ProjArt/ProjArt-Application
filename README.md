# Bienvenue dans notre application

Ce projet est un projet d'étude permettant aux étudiants de la HEIG-VD d'accéder aux horaires, à leurs absences, notes, mails et au menu de la cafétéria.



## Pour modifier et faire fonctionner l'application

Il faut run les commandes suivantes :

Veuillez entrer vos tokens pour les différents API. Le GAPS_TOKEN est un string aléatoire de 30 charactères.

Afin de lancer ces commandes veuillez créer votre base de données.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan scribe:generate
npm run dev
php artisan serve
```

## Potentielles erreurs

Il se peut que vous rencontrer une erreur aux niveaux des mails. Veuillez installer l'extension Imap si cette fonctionnalité est désirée.
