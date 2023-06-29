<?php
namespace Emma\Dbal\QueryBuilder\Expressions;

use Emma\Common\Utils\StringManagement;
use Emma\Dbal\QueryBuilder\Interfaces\WhereInterface;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class WhereCondition implements WhereInterface
{
    /**
     * @var string
     */
    protected string $paramName = "?";

    /**
     * @var string|null
     */
    protected ?string $field = null;

    /**
     * @var string [ =, !=, <, >, <=, >=, IN, NOT IN, ...]
     */
    protected string $operand = "=";

    /**
     * @var mixed
     */
    protected mixed $value;

    /**
     * @param $field
     * @param $operand
     * @param $value
     */
    protected function __construct($field, $operand, $value)
    {
        $this->setField($field)->setOperand($operand)->setValue($value)->setParamName("?");
    }

    /**
     * @param $field
     * @param $operand
     * @param $value
     * @return static
     */
    public static function compose($field, $operand, $value): static
    {
        return new self($field, $operand, $value);
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
     * @return  string
     */ 
    public function getParamName(): string
    {
        return $this->paramName;
    }

    /**
     * @param  string  $paramName
     * @return  self
     */ 
    public function setParamName(string $paramName): static
    {
        $this->paramName = str_replace(".", "", $paramName);
        return $this;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return self
     */
    public function setField(string $field): static
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return string
     */
    public function getOperand(): string
    {
        return $this->operand;
    }

    /**
     * @param string $operand
     * @return self
     */
    public function setOperand(string $operand): static
    {
        $this->operand = $operand;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (is_array($this->value)) {
            $operand = StringManagement::getOrDefault($this->operand, "IN");
            $binds = implode(", ", array_fill(0, count($this->value), $this->paramName));
            return " {$this->field} $operand ($binds) ";
        } else {
            $operand = StringManagement::getOrDefault($this->operand, "=");
            return " {$this->field} $operand {$this->paramName} ";
        }
    }

}