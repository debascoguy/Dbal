<?php
/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */

namespace Emma\Dbal;

use Emma\Common\Singleton\Singleton;
use Emma\Dbal\Connection\ConnectionProperty;
use Emma\Dbal\Connection\PDOConnection;

class ConnectionManager
{
    use Singleton;

    /**
     * @var array
     */
    private static array $connectionPool = [];

    /**
     * @param array $connectionDetails
     * @return PDOConnection
     */
    public static function createConnection(array $connectionDetails): PDOConnection
    {
        return self::getConnection(ConnectionProperty::create($connectionDetails));
    }

    /**
     * @param ConnectionProperty $property
     * @return PDOConnection
     */
    public static function getConnection(ConnectionProperty $property): PDOConnection
    {
        $serialize = serialize($property);
        if (isset(self::$connectionPool[$serialize])) {
            return self::$connectionPool[$serialize];
        }

        $connection = PDOConnection::create($property);
        self::$connectionPool[$serialize] = $connection;
        return $connection;
    }

    /**
     * @param array $multipleProperty
     * @param int $activeConnectionId
     * @return PDOConnection|null
     */
    public static function connectAll(array $multipleProperty, int $activeConnectionId = 0): ?PDOConnection
    {
        $activeConnection = null;
        foreach ($multipleProperty as $index => $property) {
            if (is_array($property)) {
                $conn = self::createConnection($property);
            } else {
                $conn = self::getConnection($property);
            }

            if ($index == $activeConnectionId) {
                $activeConnection = $conn;
            }
        }
        return $activeConnection;
    }

    /**
     * @param int $connectionId
     * @return mixed|null
     */
    public static function getConnectionByIndex(int $connectionId = 0): ?PDOConnection
    {
        $keys = array_keys(self::$connectionPool);
        if (isset(self::$connectionPool[$keys[$connectionId]])) {
            return self::$connectionPool[$keys[$connectionId]];
        }
        return null;
    }

}