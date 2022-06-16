## Test Task
```shell
git clone https://github.com/udev-21/testtask
cp .env.example .env
docker-compose up -d
docker-compose exec -it laravel bash
composer install
php artisan key:generate
php artisan migrate
```
goto -> [here](http://localhost:80/api/documentation)
