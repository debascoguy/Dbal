<?php
namespace Emma\Dbal\QueryBuilder\Expressions;

use Emma\Common\Utils\StringManagement;
use Emma\Dbal\QueryBuilder\Interfaces\NamedParamInterface;
use Emma\Dbal\QueryBuilder\Services\ParamCounter;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 *
 * WHERE IN condition is not fully supported in NamedParameter due to PDO limitation.
 * Use the simple question(?) mark WhereCondition instance for such where statement
 */
class WhereNamedCondition extends WhereCondition implements NamedParamInterface
{
    /**
     * 
     */
    protected function __construct($field, $operand, $value)
    {
        parent::__construct($field, $operand, $value);
        $this->setParamName(":".$field.ParamCounter::next());
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return static
     */
    public static function eq(string $field, mixed $value): static
    {
        return new self($field, "=", $value);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return static
     */
    public static function neq(string $field, mixed $value): static
    {
        return new self($field, "!=", $value);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return static
     */
    public static function in(string $field, mixed $value): static
    {
        return new self($field, "IN", $value);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return static
     */
    public static function notIn(string $field, mixed $value): static
    {
        return new self($field, "NOT IN", $value);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return static
     */
    public static function gt(string $field, mixed $value): static
    {
        return new self($field, ">", $value);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return static
     */
    public static function lt(string $field, mixed $value): static
    {
        return new self($field, ">", $value);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return static
     */
    public static function gte(string $field, mixed $value): static
    {
        return new self($field, ">=", $value);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return static
     */
    public static function lte(string $field, mixed $value): static
    {
        return new self($field, "<=", $value);
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        if (is_array($this->value)) {
            /**
             * WHERE IN condition is not fully supported in NamedParameter due to PDO limitation. 
             * Use the simple question(?) mark WhereCondition instance for such where statement
             */
            $operand = StringManagement::getOrDefault($this->operand, "IN");
            return " {$this->field} $operand ({$this->paramName}) ";
        } else {
            $operand = StringManagement::getOrDefault($this->operand, "=");
            return " {$this->field} $operand {$this->paramName} ";
        }
    }
}