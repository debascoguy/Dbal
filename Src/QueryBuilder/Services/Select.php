<?php
namespace Emma\Dbal\QueryBuilder\Services;

use Emma\Dbal\QueryBuilder\Constants\FetchMode;
use Emma\Dbal\QueryBuilder\Constants\QueryType;
use Emma\Dbal\QueryBuilder\Expressions\WhereCompositeCondition;
use Emma\Dbal\QueryBuilder\Expressions\WhereCondition;
use Emma\Dbal\QueryBuilder\Interfaces\NamedParamInterface;
use Emma\Dbal\QueryBuilder\Interfaces\SearchInterface;
use Emma\Dbal\QueryBuilder\Interfaces\WhereInterface;
use Emma\Dbal\QueryBuilder\Query;
use Emma\Dbal\QueryBuilder\QueryBuilder;
use Emma\Dbal\QueryBuilder\QueryExecutor;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class Select extends QueryBuilder
{
    /**
     * @var object|string
     */
    protected object|string $selectObject;

    /**
     * @var WhereCompositeCondition|WhereCondition|array
     */
    protected WhereCompositeCondition|WhereCondition|array $criteria = [];

    /**
     * @var array
     */
    protected array $criteriaDataTypes = [];

    /**
     * @var int 
     */
    protected int $fetchMode;

    /**
     * @var bool
     */
    protected bool $count = false;

    /**
     * @var string
     */
    protected string $sumColumnName = "";

    /**
     * @var array
     */
    protected array $primaryKeyFields = [];

    /**
     * @param object|string $entity
     */
    public function __construct(object|string $entity)
    {
        $this->setSelectObject($entity);
        $this->setFetchMode(FetchMode::FETCH_CLASS);
        $this->setQueryType(QueryType::SELECT_STATEMENT);
    }

    /**
     * @param Query|null $query
     * @return mixed
     * @throws \Exception
     */
    public function execute(?Query $query = null): mixed
    {
        if (!empty($query)) {
            $limit = $query->QB()->getLimit();
        } else {
            $limit = $this->getLimit();
            $query = $this->query();
        }
        return $this->executeQuery($query, $limit == 1);
    }

    /**
     * @return Query
     * @throws \Exception
     */
    public function query(): Query
    {
        $criteria = $this->getCriteria();
        $sumColumnName = $this->getSumColumnName();

        if ($this->isCount()) {
            $this->select(["count(*)" => "data_result"])
                ->from($this->getTableName())
                ->setFetchMode(FetchMode::FETCH_ASSOC);
        } elseif (!empty($sumColumnName)) {
            $this->select(["sum($sumColumnName)" => "data_result"])
                ->from($this->getTableName())
                ->setFetchMode(FetchMode::FETCH_ASSOC);
        } else if ($this->getFetchMode() == FetchMode::FETCH_ASSOC
            && count($this->primaryKeyFields) == 1
            && $this->getLimit() != 1
        ) {
            $this->setFetchMode(FetchMode::FETCH_UNIQUE);
        }
        return CriteriaHandler::handle(new Query($this), $criteria, $this->criteriaDataTypes);
    }

     /**
     * @param Query $sqlQuery
     * @param array $criteria
     * @param array $criteriaDataTypes
     * @return Query
     * @throws \Exception
     */
    public static function processWhereCondition(Query $sqlQuery, array $criteria, array $criteriaDataTypes): Query
    {
       return CriteriaHandler::processWhereCondition($sqlQuery, $criteria, $criteriaDataTypes);
    }

    /**
     * @param Query $sqlQuery
     * @param bool $isSingleResult
     * @return array|mixed|null
     */
    public function executeQuery(Query $sqlQuery, bool $isSingleResult = false): mixed
    {
        if ($isSingleResult) {
            return $this->fetchRow($sqlQuery);
        }
        return $this->fetchList($sqlQuery);
    }

    /**
     * @param Query $sqlQuery
     * @return mixed|null
     */
    protected function fetchRow(Query $sqlQuery): mixed
    {
        $data = QueryExecutor::executeSelectOne($sqlQuery);
        return empty($data) ? null : $data;
    }

    /**
     * @param Query $sqlQuery
     * @return array
     */
    protected function fetchList(Query $sqlQuery): array
    {
        return QueryExecutor::executeSelect($sqlQuery);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function count(): int
    {
        $this->setCount(true);
        $data = QueryExecutor::executeSelectOne($this->query());
        return (int)$data["data_result"];
    }

    /**
     * @param $column
     * @return int
     * @throws \Exception
     */
    public function sum($column): int
    {
        $this->setSumColumnName($column);
        $data = QueryExecutor::executeSelectOne($this->query());
        return (int)$data["data_result"];
    }

    /**
     * @return object|string
     */
    public function getSelectObject(): object|string
    {
        return $this->selectObject;
    }

    /**
     * @param object|string $selectObject
     * @return Select
     */
    public function setSelectObject(object|string $selectObject): static
    {
        $this->selectObject = $selectObject;
        return $this;
    }

    /**
     * @return WhereCompositeCondition|WhereCondition|array
     */
    public function getCriteria(): WhereCompositeCondition|WhereCondition|array
    {
        return $this->criteria;
    }

    /**
     * @param WhereCompositeCondition|WhereCondition|array $criteria
     * @return $this
     */
    public function setCriteria(WhereCompositeCondition|WhereCondition|array $criteria): static
    {
        $this->criteria = CriteriaHandler::composeCriteria($criteria);
        return $this;
    }

    /**
     * @return array
     */
    public function getCriteriaDataTypes(): array
    {
        return $this->criteriaDataTypes;
    }

    /**
     * @param array $criteriaDataTypes
     * @return Select
     */
    public function setCriteriaDataTypes(array $criteriaDataTypes): Select
    {
        $this->criteriaDataTypes = $criteriaDataTypes;
        return $this;
    }

    /**
     * @return  int
     */ 
    public function getFetchMode(): int
    {
        return $this->fetchMode;
    }

    /**
     * @param  int  $fetchMode
     * @return  self
     */ 
    public function setFetchMode(int $fetchMode): static
    {
        $this->fetchMode = $fetchMode;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isCount(): bool
    {
        return $this->count;
    }

    /**
     * @param boolean $count
     * @return Select
     */
    public function setCount(bool $count): static
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return string
     */
    public function getSumColumnName(): string
    {
        return $this->sumColumnName;
    }

    /**
     * @param string $sumColumnName
     * @return Select
     */
    public function setSumColumnName(string $sumColumnName): static
    {
        $this->sumColumnName = $sumColumnName;
        return $this;
    }

    /**
     * @return  array
     */ 
    public function getPrimaryKeyFields(): array
    {
        return $this->primaryKeyFields;
    }

    /**
     * @param  array  $primaryKeyFields
     * @return  self
     */ 
    public function setPrimaryKeyFields(array $primaryKeyFields): static
    {
        $this->primaryKeyFields = $primaryKeyFields;
        return $this;
    }
}