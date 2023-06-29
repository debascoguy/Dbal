<?php
namespace Emma\Dbal\QueryBuilder;

use Emma\Dbal\QueryBuilder\Constants\QueryType;
use Emma\Dbal\QueryBuilder\Expressions\QueryExpression;
use Emma\Dbal\QueryBuilder\Interfaces\QueryBuilderInterface;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class QueryBuilder implements QueryBuilderInterface
{
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @var int
     */
    protected $queryType = QueryType::UNSPECIFIED_STATEMENT;

    /**
     * @var array
     */
    protected $where = array();

    /**
     * @var array
     */
    protected $joins = array();

    /**
     * @var array|QueryBuilder[]
     */
    protected $union = array();

    /**
     * @var int
     */
    protected $limit = 0;

    /**
     * @var int
     */
    protected $endLimit = 0;

     /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var string
     */
    protected $appendToQuery = "";

    const ASC = "ASC";
    const DESC = "DESC";

    /**
     * @var array
     */
    protected $orderBy = array();

    /**
     * @var array
     */
    protected $groupBy = array();


    /**     ADVANCED PARAMS OPTIONS     */

    /**
     * @var array
     */
    private $tableList = array();

    /**
     * @var array
     */
    private $fieldList = array();

    /**
     * @param $tableName
     * @param string $alias
     * @param array $columns_to_alias
     * @return QueryBuilder
     */
    public function selectFrom($tableName, $alias = "", $columns_to_alias = array())
    {
        return $this->select($columns_to_alias)->from($tableName, $alias);
    }

    /**
     * @param $tableName
     * @param $alias
     * @return QueryBuilder
     */
    public function from($tableName, $alias = ""): self
    {
        return $this->setTableName($tableName, $alias);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     * @param $alias
     * @return $this
     */
    public function setTableName($tableName, $alias = "")
    {
        $this->tableName = (empty($alias)) ? $tableName : $tableName . " AS " . $alias;
        $this->tableList[] = $tableName;
        return $this;
    }

    /**
     * @param array $columns_to_alias
     * @return QueryBuilder
     */
    public function select($columns_to_alias = array())
    {
        $this->setQueryType(QueryType::SELECT_STATEMENT);
        foreach ((array)$columns_to_alias as $field => $value) {
            if (is_int($field)) {
                $this->selectColumn($value);
            } else {
                $this->selectColumn($field, $value);
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param string $tableColumnName
     * @param string $alias
     * @return $this
     */
    public function selectColumn($tableColumnName = "table_name.column_name", $alias = ""): static
    {
        $this->setQueryType(QueryType::SELECT_STATEMENT);
        $this->columns[] = (empty($alias)) ? $tableColumnName : "$tableColumnName AS $alias";
        $this->fieldList[] = $tableColumnName;
        return $this;
    }

    /**
     * @param string $tableColumnName
     * @param string $value
     * @return $this
     */
    public function updateColumn($tableColumnName = "table_name.column_name", $value = "?"): static
    {
        $this->columns[] = $tableColumnName . " = " . $value;
        $this->fieldList[] = $tableColumnName;
        $this->setQueryType(QueryType::UPDATE_STATEMENT);
        return $this;
    }

    /**
     * @param array $column_value
     * @return $this
     */
    public function update($column_value = array("table_name.column_name" => "?")): static
    {
        foreach ((array)$column_value as $field => $value) {
            $this->updateColumn($field, $value);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function delete()
    {
        $this->setQueryType(QueryType::DELETE_STATEMENT);
        return $this;
    }

    /**
     * @return array
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @param $expression
     * @param $operator
     * @return $this
     */
    public function where($expression, $operator = null): static
    {
        $this->where[] = $operator . ' ' . (string)$expression;
        return $this;
    }

    /**
     * @param $expression
     * @return $this
     */
    public function andWhere($expression): static
    {
        return $this->where($expression, " AND ");
    }

    /**
     * @param string $expression
     * @return $this
     */
    public function orWhere($expression): static
    {
        return $this->where($expression, " OR ");
    }

    /**
     * @return array
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * @param $tableName
     * @param string $condition
     * @param string $joinType
     * @return $this
     */
    public function join($tableName, string $condition, string $joinType = "INNER JOIN"): static
    {
        $this->joins[] = $joinType . " " . $tableName . " ON " . (string)$condition;
        $this->tableList[] = $tableName;
        return $this;
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @return $this
     */
    public function innerJoin($tableName, string $condition): static
    {
        return $this->join($tableName, $condition);
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @return $this
     */
    public function outerJoin($tableName, string $condition): static
    {
        return $this->join($tableName, $condition, "OUTER JOIN");
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @return $this
     */
    public function leftJoin($tableName, string $condition): static
    {
        return $this->join($tableName, $condition, "LEFT JOIN");
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @return $this
     */
    public function rightJoin($tableName, string $condition): static
    {
        return $this->join($tableName, $condition, "RIGHT JOIN");
    }

    /**
     * @return $this
     */
    public function union(): self
    {
        $QueryBuilder = new self();
        $this->union['UNION'][] = $QueryBuilder;
        return $QueryBuilder;
    }

    /**
     * @return $this
     */
    public function unionAll(): self
    {
        $QueryBuilder = new self();
        $this->union['UNION ALL'][] = $QueryBuilder;
        return $QueryBuilder;
    }

    /**
     * @return array|QueryBuilderInterface[]|QueryBuilder[]
     */
    public function getUnion(): array
    {
        return $this->union;
    }

    /**
     * @return array
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @param $tableColumn
     * @param string $sort
     * @return $this
     */
    public function orderBy($tableColumn, $sort = self::ASC): static
    {
        $this->orderBy[] = $tableColumn . " " . $sort;
        return $this;
    }
    
    /**
     * @param array $orderBy e.g: array("firstname" => "ASC", "lastname"=>"DESC")
     * @return QueryBuilder
     */
    public function setOrderBy($orderBy)
    {
        foreach ($orderBy as $tableColumn => $order_ASC_or_DESC) {
            $this->orderBy($tableColumn, $order_ASC_or_DESC);
        }
        return $this;
    }

    /**
     * @return array
     */ 
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * @param  array  $groupBy
     * @return  self
     */ 
    public function setGroupBy(array $groupBy)
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return QueryBuilder
     */
    public function setLimit($limit = 0)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     * @return QueryBuilder
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return int
     */
    public function getEndLimit()
    {
        return $this->endLimit;
    }

    /**
     * @param int $endLimit
     * @return QueryBuilder
     */
    public function setEndLimit($endLimit)
    {
        $this->endLimit = $endLimit;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppendToQuery()
    {
        return $this->appendToQuery;
    }

    /**
     * @param string $appendToQuery
     * @return QueryBuilder
     */
    public function setAppendToQuery($appendToQuery = "")
    {
        $this->appendToQuery = $appendToQuery;
        return $this;
    }

    /**
     * @param $string
     * @return $this
     */
    public function appendToQuery($string = "")
    {
        $this->appendToQuery .= $string;
        return $this;
    }

    /**
     * @return array
     */
    public function getTableList()
    {
        return $this->tableList;
    }

    /**
     * @return array
     */
    public function getFieldList()
    {
        return $this->fieldList;
    }

    /**
     * @param array $fieldList
     * @return QueryBuilder
     */
    public function setFieldList($fieldList)
    {
        $this->fieldList = $fieldList;
        return $this;
    }

    /**
     * @param $tableName
     * @param array $columnNames
     * @return QueryBuilder
     */
    public function insertInto($tableName, array $columnNames = array())
    {
        return $this->into($tableName)->insert(array_values($columnNames));
    }

    /**
     * @param array $columnNames
     * @return QueryBuilder
     */
    public function insert(array $columnNames = array())
    {
        $_this = $this->select(array_values($columnNames));
        $this->setQueryType(QueryType::INSERT_STATEMENT);
        return $_this;
    }

    /**
     * @param string $tableColumnName
     * @return $this
     */
    public function insertColumn(string $tableColumnName)
    {
        $_this = $this->selectColumn($tableColumnName);
        $this->setQueryType(QueryType::INSERT_STATEMENT);
        return $_this;
    }

    /**
     * @param $tableName
     * @return QueryBuilder
     */
    public function into($tableName)
    {
        return $this->setTableName($tableName);
    }

    /**
     * @return int
     */
    public function getQueryType()
    {
        return $this->queryType;
    }

    /**
     * @param int $queryType
     * @return QueryBuilder
     */
    public function setQueryType($queryType)
    {
        $this->queryType = $queryType;
        return $this;
    }

    /**
     * @return QueryExpression
     */
    public function expression()
    {
        return new QueryExpression();
    }

    /**
     * @return string
     */
    public function generateQuery()
    {
        return (new QueryBuilderHelper($this))->generateQuery();
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->generateQuery();
    }
    
}