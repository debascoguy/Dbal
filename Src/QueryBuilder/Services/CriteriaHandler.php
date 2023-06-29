<?php

namespace Emma\Dbal\QueryBuilder\Services;

use Emma\Dbal\QueryBuilder\Expressions\WhereCompositeCondition;
use Emma\Dbal\QueryBuilder\Expressions\WhereCondition;
use Emma\Dbal\QueryBuilder\Interfaces\NamedParamInterface;
use Emma\Dbal\QueryBuilder\Interfaces\SearchInterface;
use Emma\Dbal\QueryBuilder\Interfaces\WhereInterface;
use Emma\Dbal\QueryBuilder\Query;
use InvalidArgumentException;

class CriteriaHandler
{
    /**
     * @param Query $query
     * @param WhereCompositeCondition|WhereCondition|array $criteria
     * @param array $dataTypes
     * @return Query
     * @throws \Exception
     */
    public static function handle(
        Query $query,
        WhereCompositeCondition|WhereCondition|array $criteria,
        array $dataTypes = []
    ): Query {
        if (!empty($criteria)) {
            $criteria = $criteria instanceof WhereCompositeCondition ? $criteria : self::composeCriteria($criteria);
            $query->QB()->where($criteria);
            $criteria = $criteria->getWhereConditions();
        }
        else {
            $criteria = [];
        }
        return self::processWhereCondition($query, $criteria, $dataTypes);
    }

    /**
     * @param WhereCompositeCondition|WhereCondition|array $criteria
     * @return WhereCompositeCondition|null
     */
    public static function composeCriteria(WhereCompositeCondition|WhereCondition|array $criteria): ?WhereCompositeCondition
    {
        if ($criteria instanceof WhereCompositeCondition) {
            return $criteria;
        }
        elseif ($criteria instanceof WhereCondition) {
            $criteria = WhereCompositeCondition::andX([$criteria]);
        }
        else {
            $criteria = WhereCompositeCondition::andX($criteria);
        }
        return $criteria;
    }

    /**
     * @param Query $sqlQuery
     * @param array $criteria
     * @param array $criteriaDataTypes
     * @return Query
     * @throws \Exception
     */
    public static function processWhereCondition(Query $sqlQuery, array $criteria, array $criteriaDataTypes = []): Query
    {
        foreach ($criteria as $fieldName => $value) {
            $mainValue = $value;
            $isNamedParameter = false;
            $isSearch = false;
            $prepend = null;
            $append = null;
            if ($mainValue instanceof SearchInterface) {
                $isSearch = true;
                $prepend = $mainValue->getPrepend();
                $append = $mainValue->getAppend();
            }
            if ($mainValue instanceof WhereInterface) {
                $fieldName = $mainValue->getField();
                $value = $mainValue->getValue();
            }
            if ($mainValue instanceof NamedParamInterface) {
                $isNamedParameter = true;
                $paramName = $mainValue->getParamName();
            }
            else {
                $paramName = $fieldName;
            }

            if ($isSearch) {
                $sqlQuery->set($prepend.$value.$append, $isNamedParameter ? $paramName : null);
                continue;
            }

            $fieldDataType = $criteriaDataTypes[$fieldName] ?? gettype($value);
            if ($fieldDataType == "object" || $fieldDataType == "resource (closed)" || $fieldDataType == "resource") {
                throw new InvalidArgumentException("Invalid Parameter Data Type!");
            }

            if (is_null($value) || $fieldDataType == "NULL") {
                $sqlQuery->setNull($isNamedParameter ? $paramName : null);
            } elseif ($fieldDataType == "string") {
                foreach ((array)$value as $val) {
                    $sqlQuery->setString($val, $isNamedParameter ? $paramName : null);
                }
            } elseif ($fieldDataType == "float"
                || $fieldDataType == "double"
                || $fieldDataType == "int"
                || $fieldDataType == "integer"
            ) {
                foreach ((array)$value as $val) {
                    $sqlQuery->setNumber($val, $isNamedParameter ? $paramName : null);
                }
            } elseif ($fieldDataType == "DateTime") {
                $sqlQuery->set($value, $isNamedParameter ? $paramName : null);
            } elseif ($fieldDataType == "bool" || $fieldDataType == "boolean") {
                $sqlQuery->setBoolean($value, $isNamedParameter ? $paramName : null);
            } else {
                foreach ((array)$value as $val) {
                    $sqlQuery->set($val, $isNamedParameter ? $paramName : null);
                }
            }
        }
        return $sqlQuery;
    }
}