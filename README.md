Important: in this project we assume that store data in DB by providing http parameter "save_to_db". 

## Mortgage calculator

You should have installed XAMPP (Apache, MySQL/Postgre) or other tools to run Laravel locally. After you completed installation do next:

1. Download repository
2. Set up .env file: APP_URL (for example APP_URL=http://localhost) and DB_DATABASE connection (for example DB_DATABASE=laravel_test and user/password accordingly)
3. If you have installed composer in your system, in app root folder run: `php composer install`
3. In app root folder run: `php artisan migrate`
4. In app test folder run: `php artisan test`
5. In app root folder run: `npm install --force`
6. If http server already is running, in app root folder run: `npm run dev`


