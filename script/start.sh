#!/usr/bin/env bash
composer update &&
npm install &&
npm run hot  &
php artisan serve &
