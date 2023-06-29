<?php
namespace Emma\Dbal\QueryBuilder;

use Emma\Dbal\QueryBuilder\Constants\QueryType;
use Emma\ErrorHandler\Exception\BaseException;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class QueryBuilderHelper
{
    /**
     * @var QueryBuilder
     */
    private $QB = null;
    
    /**
     * @param \Emma\Dbal\QueryBuilder\QueryBuilder $QB
     */
    public function __construct(QueryBuilder $QB) 
    {
        $this->QB = $QB;
    }

    
    /**
     * @return QueryBuilder|null
     */
    public function getQB() 
    {
        return $this->QB;
    }

    /**
     * @param \Emma\Dbal\QueryBuilder\QueryBuilder $QB
     * @return $this
     */
    public function setQB(QueryBuilder $QB) 
    {
        $this->QB = $QB;
        return $this;
    }

    /**
     * @param $query
     * @param $data
     * @param string $statement
     * @param string $seperator
     * @return string
     */
    protected function processStatement($query, $data, $statement = "", $seperator = " ")
    {
        if (!empty($data)) {
            $query .= " $statement " . implode($seperator, $data);
        }
        return $query;
    }

    /**
     * @return string
     * @throws BaseException
     */
    public function generateQuery()
    {
        $query = match ($this->QB->getQueryType()) {
            QueryType::INSERT_STATEMENT => $this->insertStatement(),
            QueryType::UPDATE_STATEMENT => $this->updateStatement(),
            QueryType::DELETE_STATEMENT => $this->deleteStatement(),
            QueryType::SELECT_STATEMENT => $this->selectStatement(),
            default => throw new BaseException("Invalid Query Type For QueryBuilder!"),
        };
        
        $query = $this->processStatement($query, $this->QB->getJoins());
        $query = $this->processStatement($query, $this->QB->getWhere(), "WHERE");
        $query = $this->processStatement($query, addslashes($this->QB->getAppendToQuery()));
        $query = $this->processStatement($query, $this->QB->getGroupBy(), "GROUP BY", ", ");
        $query = $this->processStatement($query, $this->QB->getOrderBy(), "ORDER BY", ", ");
        
        $limit = $this->QB->getLimit();
        if (is_int($limit) && $limit > 0) {
            $query .= " LIMIT $limit ";
            $endLimit = $this->QB->getEndLimit();
            $offLimit = $this->QB->getOffset();
            if (is_int($endLimit) && $endLimit > 0) {
                $query .= ", $endLimit ";
            }
            if (is_int($offLimit) && $offLimit > 0) {
                $query .= " OFFSET $offLimit ";
            }
        }

        $union = $this->QB->getUnion();
        if (count($union) > 0) {
            foreach ($union as $unionType => $QBs) {
                /** @var QueryBuilder $QueryBuilder */
                foreach ($QBs as $QueryBuilder) {
                    $query .= " $unionType " . $QueryBuilder->generateQuery();
                }
            }
        }
        
        return $query;
    }

    /**
     * @return string
     */
    protected function selectStatement()
    {
        $tableColumns = $this->QB->getColumns();
        if (count($tableColumns) > 0){
            return "SELECT " . implode(", ", $tableColumns) . " FROM " . $this->QB->getTableName();
        }
        else{
            return "SELECT * FROM " . $this->QB->getTableName();
        }
    }

    /**
     * @return string
     * implode explanation:
     *
     * $test = array(0=>"firstname = ?",
     *              1=>"lastname = ?"
     * );
     *
     * $string = "firstname = ?, lastname=?";
     *
     */
    protected function updateStatement()
    {
        return "UPDATE " . $this->QB->getTableName() . " SET " . implode(", ", $this->QB->getColumns());
    }

    /**
     * @return string
     */
    protected function deleteStatement()
    {
        return "DELETE FROM " . $this->QB->getTableName();
    }

    /**
     * @return string
     */
    protected function insertStatement()
    {
        $sql = "INSERT INTO " . $this->QB->getTableName();
        $sql .= " ( " . implode(", ", $this->QB->getColumns()) . " ) ";
        $sql .= " VALUES ";
        $sql .= " ( ";
        $sql .= implode(", ", array_fill(0, count($this->QB->getColumns()), "?"));
        $sql .= " ) ";
        return $sql;
    }

    /**
     * @return string
     * Truncate table automatically reset the Autoincrement values to 0.
     * OR use
     * ALTER TABLE table_name AUTO_INCREMENT = 1;
     */
    protected function truncateTableStatement()
    {
        return "Truncate Table " . $this->QB->getTableName();
    }

    /**
     * @return string
     */
    protected function resetAutoIncrementStatement()
    {
        return "ALTER TABLE " . $this->QB->getTableName() . " AUTO_INCREMENT = 1";
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->generateQuery();
    }

    /**
     * @param array $realFieldNames
     * @return array
     */
    public static function getFieldNamesWithoutTableName($realFieldNames = array())
    {
        foreach ($realFieldNames as $i => $field) {
            $realFieldNames[$i] = end(explode(".", $field));
        }
        return $realFieldNames;
    }

}