<?php

namespace Emma\Dbal\QueryBuilder;

use Emma\Connection\ConnectionManager;
use Emma\Dbal\Connection\PDOConnection;
use Emma\Dbal\QueryBuilder\Constants\FetchMode;
use Emma\Dbal\QueryBuilder\Constants\QueryType;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class QueryExecutor
{
    /**
     * @param Query $sqlQuery
     * @return mixed
     */
    public static function executeSelectResult(Query $sqlQuery): mixed
    {
        $sqlQuery->setQueryType(QueryType::UNSPECIFIED_STATEMENT);
        return self::execute($sqlQuery);
    }

    /**
     * @param Query $sqlQuery
     * @param PDOConnection|null $connection
     * @return mixed
     */
    public static function execute(Query $sqlQuery, PDOConnection &$connection = null): mixed
    {
        if ($connection == null) {
            $connectionManager = ConnectionManager::getInstance();
            $connection = $connectionManager->getConnection();
            if ($connection == null) {
                $connection = $connectionManager->createConnectionByModule()->getConnection();
            }
        }
        $query = $sqlQuery->getQuery();
        $params = $sqlQuery->getParams();
        $result = $connection->executeQuery($query, $params, $sqlQuery->getFetchMode(), $sqlQuery->getFetchClassName());
        try {
            if (!$result) {
                throw new \Exception("SQL Error: -->" . $query . "<--" . json_encode($connection->getConnection()->errorInfo()));
            }

            return match ($sqlQuery->getQueryType()) {
                QueryType::INSERT_STATEMENT => $connection->getLastInsertId() > 0 ? $connection->getLastInsertId() : $result,
                QueryType::UPDATE_STATEMENT, QueryType::DELETE_STATEMENT => $connection->getNumberOfAffectedRows() > 0 ? $connection->getNumberOfAffectedRows() : $result,
                QueryType::SELECT_STATEMENT => self::fetchResult($sqlQuery, $result),
                QueryType::SELECT_ONE_STATEMENT => self::fetchResult($sqlQuery, $result, true),
                default => $result,
            };
        }
        catch (\Exception $e) {
            $connection->handlException($e, $query, $params);
            return false;
        }
    }


    /**
     * @param Query $sqlQuery
     * @return array|false|int|mixed|\PDOStatement|string
     */
    public static function executeUpdate(Query $sqlQuery): mixed
    {
        $sqlQuery->setQueryType(QueryType::UPDATE_STATEMENT);
        return self::execute($sqlQuery);
    }

    /**
     * @param Query $sqlQuery
     * @return array|false|int|mixed|\PDOStatement|string
     */
    public static function executeInsert(Query $sqlQuery): mixed
    {
        $sqlQuery->setQueryType(QueryType::INSERT_STATEMENT);
        return self::execute($sqlQuery);
    }

    /**
     * @param Query $sqlQuery
     * @return array|false|int|mixed|\PDOStatement|string
     */
    public static function executeDelete(Query $sqlQuery): mixed
    {
        $sqlQuery->setQueryType(QueryType::DELETE_STATEMENT);
        return self::execute($sqlQuery);
    }

    /**
     * @param Query $sqlQuery
     * @return array|false|int|mixed|\PDOStatement|string
     */
    public static function executeSelect(Query $sqlQuery): mixed
    {
        $sqlQuery->setQueryType(QueryType::SELECT_STATEMENT);
        return self::execute($sqlQuery);
    }

    /**
     * @param Query $sqlQuery
     * @return array|false|int|mixed|\PDOStatement|string
     */
    public static function executeSelectOne(Query $sqlQuery): mixed
    {
        $sqlQuery->setQueryType(QueryType::SELECT_ONE_STATEMENT);
        return self::execute($sqlQuery);
    }

    /**
     * @param Query $sqlQuery
     * @param \PDOStatement $result
     * @param bool $isOne
     * @return array|false|mixed|\PDOStatement
     */
    public static function fetchResult(Query $sqlQuery, \PDOStatement $result, bool $isOne = false): mixed
    {
        if ($result->rowCount() <= 0) {
            return [];
        }

        if ($sqlQuery->getFetchMode() == FetchMode::FETCH_LAZY) {
            return $result;
        }

        if ($sqlQuery->getFetchMode() == FetchMode::FETCH_CLASS) {
            return $isOne ? $result->fetch(FetchMode::FETCH_CLASS|FetchMode::FETCH_PROPS_LATE) : $result->fetchAll(FetchMode::FETCH_PROPS_LATE);
        }

        if ($sqlQuery->getFetchMode() == FetchMode::FETCH_KEY_PAIR || $sqlQuery->getFetchMode() == FetchMode::FETCH_UNIQUE) {
            return $isOne ? $result->fetch($sqlQuery->getFetchMode()) : $result->fetchAll($sqlQuery->getFetchMode());
        }

        if (!empty($sqlQuery->getFetchMode())) {
            return $isOne ? $result->fetch($sqlQuery->getFetchMode()) : $result->fetchAll($sqlQuery->getFetchMode());
        }

        return $isOne ? $result->fetch() : $result->fetchAll();
    }

}
