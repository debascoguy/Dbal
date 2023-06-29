<?php
namespace Emma\Dbal\QueryBuilder\Expressions;

use Emma\Dbal\QueryBuilder\Interfaces\SearchInterface;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class SearchCondition implements SearchInterface
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
     * @var string [ LIKE, NOT LIKE ...]
     */
    protected string $operand = "LIKE";

    /**
     * @var mixed
     */
    protected mixed $value = null;

    /**
     * @var string
     */
    protected string $append = "%";
    
    /**
     * @var string
     */
    protected string $prepend = "%";

    /**
     * @param string $field
     * @param string $operand
     * @param mixed $value
     * @param string $append
     * @param string $prepend
     */
    protected function __construct(string $field, string $operand, mixed $value, string $append = "%", string $prepend = "%")
    {
        $this->setField($field)
            ->setOperand($operand)
            ->setValue($value)
            ->setPrepend($prepend)
            ->setAppend($append)
            ->setParamName(" ? ");
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $append
     * @param string $prepend
     * @return SearchCondition
     */
    public static function like(string $field, mixed $value, string $append = "%", string $prepend = "%"): static
    {
        return new self($field, "LIKE", $value, $append, $prepend);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $append
     * @param string $prepend
     * @return static
     */
    public static function notLike(string $field, mixed $value, string $append = "%", string $prepend = "%"): static
    {
        return new self($field, "NOT LIKE", $value, $append, $prepend);
    }

    /**
     * @return string
     */
    public function getParamName(): string
    {
        return $this->paramName;
    }

    /**
     * @param string $paramName
     * @return $this
     */
    public function setParamName(string $paramName): static
    {
        $this->paramName = $paramName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * @param string|null $field
     * @return $this
     */
    public function setField(?string $field): static
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
     * @return $this
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
    public function getAppend(): string
    {
        return $this->append;
    }

    /**
     * @param string $append
     * @return $this
     */
    public function setAppend(string $append): static
    {
        $this->append = $append;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrepend(): string
    {
        return $this->prepend;
    }

    /**
     * @param string $prepend
     * @return $this
     */
    public function setPrepend(string $prepend): static
    {
        $this->prepend = $prepend;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return " {$this->field} {$this->operand} {$this->paramName} ";
    }
}