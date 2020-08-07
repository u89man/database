## Database 0.1.2

##### Установка

```
composer require u89man/database
```

##### Примеры

```php
use U89Man\Database\DB;

$host = 'localhost';
$dbname = 'database';
$user = 'user';
$passwd = 'password';

$dsn = 'mysql:dbname='.$dbname.';host='.$host;

$options = [
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    PDO::ATTR_CASE => PDO::CASE_NATURAL,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];

$pdo = new PDO($dsn, $user, $passwd, $options);

$db = new DB($pdo);

```


