# Bienvenue dans notre application

Ce projet est un projet d'étude permettant aux étudiants de la HEIG-VD d'accéder aux horaires, à leurs absences, notes, mails et au menu de la cafétéria.



## Pour modifier et faire fonctionner l'application

Il faut run les commandes suivantes :

Veuillez entrer vos tokens pour les différents API. Le GAPS_TOKEN est un string aléatoire de 30 charactères.

```bash
composer install
npm install
npm run dev
cp .env.example .env
php artisan key:generate
php artisan serve
```
