<?php

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */

namespace Emma\Dbal\Connection;

use Emma\Common\Utils\StringManagement;
use Emma\Dbal\Connection;
use PDOException;
use const PHP_EOL;

class PDOConnection {

    /**
     * @var int the current transaction depth
     */
    protected int $transactionLevel = 0;

    /**
     * @var \PDO|null
     */
    private ?\PDO $connection;

    /**
     * @var int
     */
    private int $numberOfAffectedRows = 0;

    /**
     * @var \PDOStatement|null
     */
    private ?\PDOStatement $statement = null;

    /**
     * @var ConnectionProperty
     */
    private ConnectionProperty $connectionProperty;

    /**
     * @param ConnectionProperty $property
     * @return $this
     */
    public static function create(ConnectionProperty $property): static
    {
        $self = new self();
        return $self->connect($property);
    }

    /**
     * Test if database driver support savepoints
     *
     * @return bool
     */
    protected function canSavepoint(): bool
    {
        return in_array($this->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME), Drivers::SAVE_POINT_SUPPORTED_DRIVERS);
    }

    /**
     * @return int
     * Only for supported drivers
     */
    public function savepoint(): int
    {
        return $this->getConnection()->exec("SAVEPOINT LEVEL{$this->transactionLevel}");
    }

    /**
     * @return int
     * Only for supported drivers
     */
    public function release_savepoint(): int
    {
        return $this->getConnection()->exec("RELEASE SAVEPOINT LEVEL{$this->transactionLevel}");
    }

    /**
     * @return bool|int
     */
    public function beginTransaction(): bool|int
    {
        if (!$this->getConnection()->inTransaction()) {
            $status = ($this->transactionLevel == 0 || !$this->canSavepoint()) ?
                    $this->getConnection()->beginTransaction() :
                    $this->savepoint();
            $this->transactionLevel++;
            return $status;
        }
        return true;
    }

    /**
     * @return bool|int
     */
    public function commit(): bool|int
    {
        $this->transactionLevel--;
        return ($this->transactionLevel == 0 || !$this->canSavepoint()) ?
                $this->getConnection()->commit() :
                $this->release_savepoint();
    }

    /**
     * @return bool|int
     */
    public function rollback(): bool|int
    {
        if ($this->transactionLevel == 0) {
            throw new \PDOException('Rollback error : There is no transaction started');
        }

        $this->transactionLevel--;
        return ($this->transactionLevel == 0 || !$this->canSavepoint()) ?
                $this->getConnection()->rollBack() :
                $this->getConnection()->exec("ROLLBACK TO SAVEPOINT LEVEL{$this->transactionLevel}");
    }

    /**
     * @return bool
     */
    public function rollBackAll(): bool
    {
        if ($this->transactionLevel == 0 || !$this->canSavepoint()) {
            return $this->getConnection()->rollBack();
        }
        else {
            $TL = $this->getTransactionLevel();
            $i = 0;
            while($i < $TL) {
                $this->rollback();
                $i++;
            }
        }
        return true;
    }

    /**
     * @return int
     */
    public function getTransactionLevel(): int
    {
        return $this->transactionLevel;
    }

    /**
     * @param ConnectionProperty $connectionProperty
     * @return $this
     */
    public function connect(ConnectionProperty $connectionProperty): static
    {
        $this->setConnectionProperty($connectionProperty);
        $dsn = $connectionProperty->getDsn() ?? "{dbms}:host={host};port={port};dbname={db}";
        $connectionString = str_replace([
                "{dbms}", 
                "{host}", 
                "{port}", 
                "{db}"
            ], [
                $connectionProperty->getDriver(),
                $connectionProperty->getHost(),
                $connectionProperty->getPort(),
                $connectionProperty->getDbname()
            ], 
            $dsn
        );

        $this->connection = new \PDO($connectionString, $connectionProperty->getUser(), $connectionProperty->getPassword());
        // set the PDO error mode to exception
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        if ($connectionProperty->getDriver() == Connection\Drivers::MYSQL) {
            //Setting the connection character set to UTF-8 prior to PHP 5.3.6
            $this->connection->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
        }
        return $this;
    }

    /**
     * @close Connection
     */
    public function close(): void
    {
        $this->connection = null;
    }

    /**
     * @param string $sql
     * @param array $params
     * @param int $fetchMode
     * @param $fetchClassName
     * @return \PDOStatement|null
     */
    public function executeQuery(string $sql, array $params = array(), int $fetchMode = \PDO::FETCH_ASSOC, $fetchClassName = null): \PDOStatement|null
    {
        if (is_null($fetchClassName)) {
            $fetchMode = \PDO::FETCH_ASSOC;
        }
        
        try {
            if (empty($params)) {
                $this->statement = $this->connection->query($sql, $fetchMode);
            } else {
                $this->statement = StringManagement::contains($sql, "?") ?
                        /** SQL statement template with question mark parameters */
                        $this->connection->prepare($sql) :
                        /** SQL statement template with named parameters */
                        $this->connection->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
                $this->statement->execute($params);
            }
            if (!empty($fetchClassName)){
                $this->statement->setFetchMode($fetchMode, $fetchClassName);
            }
            else{
                $this->statement->setFetchMode($fetchMode);
            }
            $this->numberOfAffectedRows = $this->statement->rowCount();
            return $this->statement;
        } catch (\PDOException $e) {
            if (!is_null($this->statement)) {
                $message = "PDO Statement Error Code: " . $this->statement->errorCode() . PHP_EOL;
                $message .= "PDO Statement Error Message: " . json_encode($this->statement->errorInfo()) . PHP_EOL;
            }
            $message .= "Exception Code: " . $e->getCode() . PHP_EOL;
            $message .= "Exception File: " . $e->getFile() . PHP_EOL;
            $message .= "Exception Line: " . $e->getLine() . PHP_EOL;
            $message .= "Exception Message: " . $e->getMessage() . PHP_EOL;
            $this->error($sql, $message);
            return null;
        }
    }

    /**
     * @return int|string
     */
    public function getLastInsertID(): int|string
    {
        try {
            return $this->connection->lastInsertId();
        }
        catch(PDOException $e) {
            return 0;
        }
    }

    /**
     * @param int $resultType
     * @return bool
     */
    public function setFetchMode(int $resultType = \PDO::FETCH_ASSOC): bool
    {
        if ($this->isPDOStatement($this->statement)) {
            return $this->statement->setFetchMode($resultType);
        } else {
            self::logError("Error: No Result Found for Fetch Array");
            return false;
        }
    }

    /**
     * @return bool
     */
    public function setFetchModeAsAssoc(): bool
    {
        return $this->setFetchMode();
    }

    /**
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    /**
     * @param \PDO $connection
     * @return PDOConnection
     */
    public function setConnection(\PDO $connection): static
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfAffectedRows(): int
    {
        return $this->numberOfAffectedRows;
    }

    /**
     * @param int $numberOfAffectedRows
     * @return PDOConnection
     */
    public function setNumberOfAffectedRows(int $numberOfAffectedRows): static
    {
        $this->numberOfAffectedRows = $numberOfAffectedRows;
        return $this;
    }

    /**
     * @return \PDOStatement
     */
    public function getStatement(): \PDOStatement
    {
        return $this->statement;
    }

    /**
     * @param \PDOStatement $statement
     * @return PDOConnection
     */
    public function setStatement(\PDOStatement $statement): static
    {
        $this->statement = $statement;
        return $this;
    }

    /**
     * @param $result
     * @return bool
     */
    public function isPDOStatement($result): bool
    {
        return ($result instanceof \PDOStatement);
    }

    /**
     * @param $connection
     * @return bool
     */
    public function isConnection($connection): bool
    {
        return ($connection instanceof \PDO);
    }

    
    /**
     * @return  ConnectionProperty
     */ 
    public function getConnectionProperty(): ConnectionProperty
    {
        return $this->connectionProperty;
    }

    /**
     * @param  ConnectionProperty  $connectionProperty
     * @return  self
     */ 
    public function setConnectionProperty(ConnectionProperty $connectionProperty): static
    {
        $this->connectionProperty = $connectionProperty;
        return $this;
    }

    /**
     * @param \Exception $e
     * @param $query
     * @param $params
     * @return void
     */
    public function handlException(\Exception $e, $query, $params)
    {
        $message  = "Params: " . json_encode($params) . PHP_EOL;
        $message .= "PDO Statement Error Message: " . json_encode($this->getConnection()->errorInfo()) . PHP_EOL;
        $message .= "Exception Code: " . $e->getCode() . PHP_EOL;
        $message .= "Exception File: " . $e->getFile() . PHP_EOL;
        $message .= "Exception Line: " . $e->getLine() . PHP_EOL;
        $message .= "Exception Message: " . $e->getMessage() . PHP_EOL;
        $this->error($query, $message);
    }

    /**
     * @param string $query
     * @param string $error
     * @return string
     */
    public function error(string $query = '', string $error = ""): string
    {
        if ($query) {
            echo $query . '<br>';
        }

        $msg = "QUERY: \n$query\nERROR MESSAGE: \n" . $error . "\nTRACE:\n==============\n";
        $backtrace = debug_backtrace();
        $msg .= "<b>Error in file:</b> {$backtrace[1]['file']}  <b>[Line #:{$backtrace[1]['line']}]</b>\n"
                . "<b>Function:</b> {$backtrace[1]['function']}\n<b>{$error}</b>";
        echo nl2br($msg) . '<br>';
        self::logError($msg);
        return $error;
    }

    /**
     * @param string $msg
     */
    public static function logError($msg)
    {
        $msg = strip_tags($msg);
        $msg .= "\nRemote IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
        error_log('SQL ERROR: ' . strip_tags($msg), 0);
    }

}
