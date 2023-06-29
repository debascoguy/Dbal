<?php

namespace Emma\Dbal\QueryBuilder\Expressions;

use Emma\Dbal\QueryBuilder\Interfaces\ExpressionInterface;

class QueryExpression implements ExpressionInterface {

    const EQUAL = "=";

    const NOT_EQUAL = "!=";

    const LESS_THAN = "<";

    const GREATER_THAN = ">";

    const LESS_THAN_OR_EQUAL_TO = "<=";

    const GREATER_THAN_OR_EQUAL_TO = ">=";

    const IN = "IN";

    const NOT_IN = "NOT IN";

    const LIKE = "LIKE";

    const NOT_LIKE = "NOT LIKE";


    /**
     * @param string $field
     * @param string $operator
     * @param string|int|float|null $value
     * @return string
     */
    public static function comparison(string $field, string $operator, string|int|float|null $value): string
    {
        return $field.' '.$operator.' '.$value;
    }

    /**
     * @param string $field
     * @param string|int|float|null $value
     * @return string
     */
    public static function eq(string $field, string|int|float|null $value): string
    {
        return self::comparison($field, self::EQUAL, $value);
    }

    /**
     * @param string $field
     * @param string|int|float|null $value
     * @return string
     */
    public static function neq(string $field, string|int|float|null $value): string
    {
        return self::comparison($field, self::NOT_EQUAL, $value);
    }

    /**
     * @param string $field
     * @param string|int|float|null $value
     * @return string
     */
    public static function lt(string $field, string|int|float|null $value): string
    {
        return self::comparison($field, self::LESS_THAN, $value);
    }

    /**
     * @param string $field
     * @param string|int|float|null $value
     * @return string
     */
    public static function lte(string $field, string|int|float|null $value): string
    {
        return self::comparison($field, self::LESS_THAN_OR_EQUAL_TO, $value);
    }

    /**
     * @param string $field
     * @param string|int|float|null $value
     * @return string
     */
    public static function gt(string $field, string|int|float|null $value): string
    {
        return self::comparison($field, self::GREATER_THAN, $value);
    }

    /**
     * @param string $field
     * @param string|int|float|null $value
     * @return string
     */
    public static function gte(string $field, string|int|float|null $value): string
    {
        return self::comparison($field, self::GREATER_THAN_OR_EQUAL_TO, $value);
    }

    /**
     * @param string $field
     * @param array $value
     * @return string
     */
    public static function in(string $field, array $value): string
    {
        return self::comparison($field, self::IN, "('".implode("', '", $value)."')");
    }

    /**
     * @param string $field
     * @param array $value
     * @return string
     */
    public static function notIn(string $field, array $value): string
    {
        return self::comparison($field, self::NOT_IN, "('".implode("', '", $value)."')");
    }

    /**
     * @param string $field
     * @param string|int|float|null $value
     * @return string
     */
    public static function like(string $field, string|int|float|null $value): string
    {
        return self::comparison($field, self::LIKE, $value);
    }

    /**
     * @param string $field
     * @param string $value
     * @return string
     */
    public static function notLike(string $field, string $value): string
    {
        return self::comparison($field, self::NOT_LIKE, $value);
    }

    /**
     * @param mixed|null $args
     * @return QueryCompositeExpression
     */
    public static function andX(mixed $args = null): QueryCompositeExpression
    {
        $parts = func_get_args();
        return QueryCompositeExpression::create(QueryCompositeExpression::AND, $parts);
    }

    /**
     * @param mixed|null $args
     * @return QueryCompositeExpression
     */
    public static function orX(mixed $args = null): QueryCompositeExpression
    {
        $parts = func_get_args();
        return QueryCompositeExpression::create(QueryCompositeExpression::OR, $parts);
    }        

    public function __toString()
    {
        return "";
    }
}