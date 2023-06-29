<?php
namespace Emma\Dbal\Connection;

use Emma\Common\Utils\StringManagement;

/**
 * Class ConnectionProperty
 */
class ConnectionProperty
{
    private string $host = 'localhost';

    private string $user = 'root';

    private string $password = '';

    private string $dbname = '';
    private int $port = 3306;
    private string $socket = '';

    private string $driver = Drivers::MYSQL; //There are other fully supported dbms inside the DBMS class...
    private string $dsn = ""; // If connection failed, try specifying the PDO connection string using the 'dsn' field.

    private string $schema = ''; //Postgres Users with different schemas


    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param string $driver
     * @param int $port
     * @param string $socket
     * @param string $dsn
     */
    private function __construct(
        string $host,
        string $user,
        string $password,
        string $dbname,
        string $driver = Drivers::MYSQL,
        int    $port = 3306,
        string $socket = '',
        string $dsn = ""
    ) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->port = !empty($port) ? $port : 3306;
        $this->socket = !empty($socket) ? $socket : '';
        $this->driver = !empty($driver) ? $driver : Drivers::MYSQL;
        $this->dsn = StringManagement::stripSpace($dsn);
    }

    /**
     * @param array $connectionDetails
     * @return ConnectionProperty
     */
    public static function create(array $connectionDetails): ConnectionProperty
    {
        return new static(
            $connectionDetails["host"],
            $connectionDetails["user"],
            $connectionDetails["password"],
            $connectionDetails["dbname"],
            $connectionDetails["driver"] ?? Drivers::MYSQL,
            $connectionDetails["port"] ??  3306,
            $connectionDetails["socket"] ?? "",
            $connectionDetails["dsn"] ?? "{dbms}:host={host};port={port};dbname={db}"
        );
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function setHost(string $host): static
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @return $this
     */
    public function setUser(string $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbname(): string
    {
        return $this->dbname;
    }

    /**
     * @param string $dbname
     * @return $this
     */
    public function setDbname(string $dbname): static
    {
        $this->dbname = $dbname;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return $this
     */
    public function setPort(int $port): static
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getSocket(): string
    {
        return $this->socket;
    }

    /**
     * @param string $socket
     * @return $this
     */
    public function setSocket(string $socket): static
    {
        $this->socket = $socket;
        return $this;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @param string $driver
     * @return $this
     */
    public function setDriver(string $driver): static
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return string
     */
    public function getDsn(): string
    {
        return $this->dsn;
    }

    /**
     * @param string $dsn
     * @return $this
     */
    public function setDsn(string $dsn): static
    {
        $this->dsn = StringManagement::stripSpace($dsn);
        return $this;
    }

    /**
     * @return string
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * @param string $schema
     * @return $this
     */
    public function setSchema(string $schema): static
    {
        $this->schema = $schema;
        return $this;
    }
}
