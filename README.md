# Hello Secretlab 👋

I am excited to present what I have created following your guidelines. The code coverage for the business logic is at 100%. It is verified at each deployment and automatically released to production with every push to the 'main' branch.

The site is hosted at: https://secretlab.remyguillermic.com/. You can access the API through the following endpoints:

- POST https://secretlab.remyguillermic.com/object
- GET https://secretlab.remyguillermic.com/object/get_all_records
- GET https://secretlab.remyguillermic.com/object/{key}?timestamp=

## Installation ⚙️

To set up the project locally, follow these steps:

```shell
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

### Run tests 🧪

To execute the test suite with coverage, use the following command:

```shell
php artisan test -p --coverage --min=90
```