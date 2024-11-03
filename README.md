## Simple CRUD app *User Service*

The app is meant to be a storage of users and their login credentials.
Users can update login data of themselves, admins can update anyone's data.

The app is hosted inside Docker containers which can be run using
```bash
docker-compose up -d
```

To setup the app you should install dependencies:
```bash
composer install
```
and generate JWT keys:
```bash
php bin/console lexik:jwt:generate-keypair
```

Then you can setup the database:
```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

You also can find Postman collection (`user-service.postman_collection`) and the MySQL dump (`dump.sql`) in the root directory

## Простий застосунок CRUD *User Service*

Застосунок призначений для зберігання даних користувачів і їхніх облікових даних.
Користувачі можуть оновлювати свої дані для входу, адміністратори можуть оновлювати дані будь-кого.

Програма розміщена в контейнерах Docker, які можна запустити за допомогою
```bash
docker-compose up -d
```

Щоб налаштувати додаток, слід встановити залежності:
```bash
composer install
```
і згенерувати ключі JWT:
```bash
php bin/console lexik:jwt:generate-keypair
```

Потім ви можете налаштувати базу даних:
```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

Ви також можете знайти колекцію Postman (`user-service.postman_collection`) і дамп MySQL (`dump.sql`) у рут директорії.
