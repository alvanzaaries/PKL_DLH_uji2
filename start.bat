@echo off
echo Checking vendor folder...
if not exist vendor (
    echo Vendor folder not found. Installing dependencies...
    composer install --ignore-platform-reqs
    echo.
) else (
    echo Vendor folder exists. Skipping composer install.
)

echo.
echo Checking .env file...
if not exist .env (
    echo .env file not found. Creating from .env.example...
    copy .env.example .env
    php artisan key:generate
    echo.
) else (
    echo .env file exists.
)

echo.
echo Starting Laravel development server...
php artisan serve
