# Dbal
Emma Database Access Layer. Rely solely on the in-built PHP PDO and FAST. Also, comes with advanced Query Builder for more flexibility and searching and with parameterized queries to prevent against sqli or other sql attacks

#Example:
```
$connection = ConnectionManager::createConnection([
          "host" => "localhost",
            "username" => "root",
            "password" => "",
            "dbname" => "mytest",
            "port" => 3306,
            "driver" => \Emma\Dbal\Connection\Drivers::MYSQL
]);

$sqlQuery = new Query();
$sqlQuery->QB()->delete()->from("table");
$criteria = []; //KeyValue pair
$dataTypes = []; //Optional
$sqlQuery = CriteriaHandler::handle($sqlQuery, $criteria, $dataTypes);

$sqlQuery->execute();

```

More documentation coming...
