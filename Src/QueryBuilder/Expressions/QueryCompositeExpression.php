<?php

namespace Emma\Dbal\QueryBuilder\Expressions;

use Emma\Dbal\QueryBuilder\Interfaces\ExpressionInterface;

class QueryCompositeExpression implements ExpressionInterface, \Countable {

    const AND = "AND";
    const OR = "OR";

    /**
     * @var string|null
     */
    protected ?string $operator = null;

    /**
     * @var array
     */
    protected array $parts =  [];

    /**
     * @param string $operator
     * @param array $parts
     */
    private function __construct(string $operator, array $parts = array())
    {
        $this->operator = $operator;
        $this->addMultiple($parts);
    }

    /**
     * @param string $operator
     * @param array $parts
     * @return QueryCompositeExpression
     */
    public static function create(string $operator, array $parts = []): static
    {
        return new self($operator, $parts);
    }

    /**
     * @param array|self[] $parts
     */
    public function addMultiple(array $parts = array()): self
    {
        foreach((array)$parts as $part) {
            $this->add($part);
        }
        return $this;
    }

    /**
     * @param QueryCompositeExpression|string $part
     * @return $this
     */
    public function add(QueryCompositeExpression|string $part): self
    {
        if (!empty($part) || ($part instanceof self && $part->count() > 0)) {
            $this->parts[] = $part;
        }
        return $this;
    }
    
    /**
     * @return  string
     */ 
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param  string  $operator
     * @return  self
     */ 
    public function setOperator(string $operator): self
    {
        $this->operator = $operator;
        return $this;
    }

    /**
     * @return  array
     */ 
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @param  array  $parts
     * @return  self
     */ 
    public function setParts(array $parts): self
    {
        $this->parts = $parts;
        return $this;
    }

    /**
     * @return integer
     */
    public function count(): int
    {
        return count($this->parts);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->count() == 1) {
            return (string)$this->parts[0];
        } 
        return "(" . implode(") ".$this->operator." (", $this->parts) . ")";
    }
}