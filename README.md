<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://files.joseperezgil.com/images/snappyshop/logo.png" width="150" style="border-radius: 20px;" alt="Snappyshop Logo"></a></p>
<p align="center" style="font-weight: bold; font-size: 32px">SnappyShop API</p>

### Initial Setup After Cloning the Platform

Follow these steps to set up the platform after cloning it:

1. Install Composer dependencies:
    ```bash
    composer install
    ```
2. Copy the example environment file and rename it:
    ```bash
    cp .env.example .env
    ```
3. Generate the application key:
    ```bash
    php artisan key:generate
    ```
4. Generate the JWT secret:
    ```bash
    php artisan jwt:secret
    ```
5. Run the migrations and seeders to populate the database:
    ```bash
    php artisan migrate --seed
    ```
