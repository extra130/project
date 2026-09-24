Set-Location -Path "C:\xampp\htdocs\project"

php artisan backup:clean
php artisan backup:run --only-db