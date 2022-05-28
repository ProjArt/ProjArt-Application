## How to start the application

Il faut run les commandes suivantes :

```bash
composer install
npm install
npm run dev
cp .env.example .env
php artisan key:generate
echo 'GAPS_TOKEN="VGrxhPp9xuDb947JZADe7ESdBDkwdq"' >> .env
php artisan serve
```
