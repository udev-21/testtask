## Test Task
```shell
git clone https://github.com/udev-21/upgraded-palm-tree
cp .env.example .env
docker-compose up -d
docker-compose exec -it laravel.test bash
composer install
php artisan key:generate
php artisan migrate
npm install
npm run development
```
goto -> [here](http://localhost:80)
