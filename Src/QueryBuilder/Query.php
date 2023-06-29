<?php
namespace Emma\Dbal\QueryBuilder;

use Emma\Dbal\QueryBuilder\Constants\FetchMode;
use Emma\Dbal\QueryBuilder\Constants\QueryType;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class Query
{
    /**
     * @var string|QueryBuilder
     */
    protected string|QueryBuilder $query = "";

    /**
     * @var array
     */
    protected array $params = [];

    /**
     * @var array
     */
    protected array $paramTypes = [];

    /**
     * @var int
     */
    protected int $fetchMode = FetchMode::FETCH_ASSOC;

    /**
     * @var object|string|null
     */
    protected object|string|null $fetchClassName = null;

    /**
     * @var int
     */
    protected int $paramCount = 0;

    /**
     * @var int
     */
    public int $queryType = QueryType::UNSPECIFIED_STATEMENT;

    /**
     * @param $query
     */
    public function __construct($query = null)
    {
        $this->setQuery((string)$query);
    }

    /**
     * @return QueryBuilder
     */
    public function QB()
    {
        if (!$this->query instanceof QueryBuilder) {
            $this->query = new QueryBuilder();
        }
        return $this->query;
    }

    /**
     * @param $name
     * @throws \Exception
     */
    public function setNull($name): void
    {
        $this->set(null, $name);
    }

    /**
     * @param $value
     * @param $name
     * @throws \Exception
     */
    public function setString($value, $name = null): void
    {
        $this->set(is_null($value) ? $value : (String)$value, $name);
    }

    /**
     * @param $value
     * @param $name
     * @return $this
     * @throws \Exception
     */
    public function setBoolean($value, $name = null): static
    {
        $value2 = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        return $this->set(is_null($value) ? $value : $value2, $name);
    }

    /**
     * @param $value
     * @param $name
     * @return $this
     * @throws \Exception
     */
    public function setNumber($value, $name = null): static
    {
        if (!is_numeric($value) && !is_null($value)) {
            throw new \Exception($value . ' is not a number');
        }
        $this->set($value, $name);
        return $this;
    }

    /**
     * @param $value
     * @param $name
     * @return $this
     * @throws \Exception
     */
    public function set($value, $name = null)
    {
        if (empty($name)) {
            $this->params[] = $value;
            $this->paramCount++;
            return $this;
        }
        
        if (!empty($this->params[$name])) {
            $this->params[$name] = array_merge((array)$this->params[$name], (array)$value);             
        }
        else {
            $this->params[$name] = $value;
        }
        $this->paramCount++;
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     * @throws \Exception
     */
    public function setArray(array $values = array()): static
    {
        foreach ($values as $value) {
            $this->set($value);
        }
        return $this;
    }

    /**
     * @param $query
     */
    public function setQuery($query): void
    {
        $this->query = (string)$query;
    }

    /**
     * @return string
     */
    public function printQuery(): string
    {
        return vsprintf(str_replace("?", "%s", $this->query), $this->params);
    }


    /**
     * @return string
     */
    public function getQuery(): string
    {
        return (string)$this->query;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return $this
     * @throws \Exception
     */
    public function setParams(array $params): static
    {
        return $this->setArray($params);
    }

    /**
     * @return array
     */
    public function getParamTypes(): array
    {
        return $this->paramTypes;
    }

    /**
     * @param array $paramTypes
     * @return $this
     */
    public function setParamTypes(array $paramTypes): static
    {
        $this->paramTypes = $paramTypes;
        return $this;
    }

    /**
     * @return int
     */
    public function getParamCount(): int
    {
        return $this->paramCount;
    }

    /**
     * @return int
     */
    public function getQueryType(): int
    {
        return $this->queryType;
    }

    /**
     * @param int $queryType
     * @return Query
     */
    public function setQueryType(int $queryType): static
    {
        $this->queryType = $queryType;
        return $this;
    }

    /**
     * @return int
     */
    public function getFetchMode(): int
    {
        return $this->fetchMode;
    }

    /**
     * @param int $fetchMode
     * @return Query
     */
    public function setFetchMode(int $fetchMode): static
    {
        $this->fetchMode = $fetchMode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFetchClassName(): ?string
    {
        return $this->fetchClassName;
    }

    /**
     * @param string|null $fetchClassName
     * @return $this
     */
    public function setFetchClassName(?string $fetchClassName): static
    {
        $this->fetchClassName = $fetchClassName;
        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function execute(): mixed
    {
        return QueryExecutor::execute($this);
    }

    /**
     * @param array $keyValues
     * @param array $dataTypes
     * @return void
     * @throws \Exception
     */
    public function bindParams(array $keyValues, array $dataTypes): void
    {
        foreach($keyValues as $field => $value) {
            $fieldDataType = $dataTypes[$field] ?? gettype($value);
            if ($fieldDataType == "object" || $fieldDataType == "resource (closed)" || $fieldDataType == "resource") {
                throw new \InvalidArgumentException("Invalid Parameter Data Type!");
            }

            if (is_null($value) || $fieldDataType == "NULL") {
                $this->setNull(null);
            } elseif ($fieldDataType == "string") {
                foreach ((array)$value as $val) {
                    $this->setString($val);
                }
            } elseif ($fieldDataType == "float"
                || $fieldDataType == "double"
                || $fieldDataType == "int"
                || $fieldDataType == "integer"
            ) {
                foreach ((array)$value as $val) {
                    $this->setNumber($val);
                }
            } elseif ($fieldDataType == "DateTime") {
                $this->set($value);
            } elseif ($fieldDataType == "bool" || $fieldDataType == "boolean") {
                $this->setBoolean($value);
            } else {
                foreach ((array)$value as $val) {
                    $this->set($val);
                }
            }
        }
    }

}