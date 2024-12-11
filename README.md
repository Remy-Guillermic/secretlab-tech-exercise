## Installation :

```shell
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

### Run tests :
```shell
php artisan test -p --coverage --min=85
```