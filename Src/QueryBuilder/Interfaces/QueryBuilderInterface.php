<?php

namespace Emma\Dbal\QueryBuilder\Interfaces;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 * Date: 2/5/2018
 * Time: 2:36 PM
 */
interface QueryBuilderInterface
{
    /**
     * @return string
     */
    public function getTableName();

    /**
     * @param string $tableName
     * @return QueryBuilderInterface
     */
    public function setTableName($tableName);

    /**
     * @param array $columns_to_alias
     * @return $this
     */
    public function select($columns_to_alias = array());

    /**
     * @return array
     */
    public function getColumns();

    /**
     * @param string $tableName
     * @param string $alias
     * @return QueryBuilderInterface
     */
    public function from($tableName, $alias = "");

    /**
     * @param string $tableColumnName
     * @param string $alias
     * @return $this
     */
    public function selectColumn($tableColumnName = "table_name.column_name", $alias = "");

    /**
     * @param string $tableColumnName
     * @param string $value
     * @return $this
     */
    public function updateColumn($tableColumnName = "table_name.column_name", $value = "?");

    /**
     * @param array $column_value
     * @return $this
     */
    public function update($column_value = array("table_name.column_name" => "?"));

    /**
     * @return $this
     */
    public function delete();

    /**
     * @return array
     */
    public function getWhere();

    /**
     * @param string $tableColumn
     * @param string $operator
     * @return $this
     */
    public function where($expression, $operator = null);

    /**
     * @param string $tableColumn
     * @return $this
     */
    public function andWhere($expression);

    /**
     * @param string $expression
     * @return $this
     */
    public function orWhere($expression);

    /**
     * @return array
     */
    public function getJoins();

    /**
     * @param string $tableName
     * @param string $condition
     * @param string $joinType
     * @return $this
     */
    public function join($tableName, string $condition, string $joinType = "INNER JOIN");

    /**
     * @param string $tableName
     * @param string $condition
     * @return $this
     */
    public function innerJoin($tableName, string $condition);

    /**
     * @param string $tableName
     * @param string $condition
     * @return $this
     */
    public function outerJoin($tableName, string $condition);

    /**
     * @param string $tableName
     * @param string $condition
     * @return $this
     */
    public function leftJoin($tableName, string $condition);

    /**
     * @param string $tableName
     * @param string $condition
     * @return $this
     */
    public function rightJoin($tableName, string $condition);

    /**
     * @return QueryBuilderInterface
     */
    public function union();

    /**
     * @return QueryBuilderInterface
     */
    public function unionAll();

    /**
     * @return array|QueryBuilderInterface[]
     */
    public function getUnion();

    /**
     * @return string
     */
    public function getOrderBy();

    /**
     * @param $tableColumn
     * @param string $sort
     */
    public function orderBy($tableColumn, $sort = "ASC");

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param int $limit
     * @return QueryBuilderInterface
     */
    public function setLimit($limit);

    /**
     * @return string
     */
    public function getAppendToQuery();

    /**
     * @param string $appendToQuery
     * @return QueryBuilderInterface
     */
    public function setAppendToQuery($appendToQuery);

    /**
     * @param $string
     * @return $this
     */
    public function appendToQuery($string = "");

    /**
     * @return array
     */
    public function getFieldList();

    /**
     * @return array
     */
    public function getTableList();

    /**
     * @return string
     */
    public function generateQuery();
}