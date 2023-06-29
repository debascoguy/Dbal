<?php
namespace Emma\Dbal\QueryBuilder\Expressions;

use Emma\Dbal\QueryBuilder\Interfaces\WhereInterface;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class WhereCompositeCondition implements WhereInterface
{
    /**
     * @var array
     */
    protected array $compositeConditions = [];

    /**
     * @var string
     * Default to AND...But can be changed
     */
    protected string $operand = "AND"; //

    /**
     */
    private function __construct()
    { 
    }

    /**
     * @param array $conditions
     * @param string $operand
     * @return static
     */
    public static function compose(array $conditions, string $operand): static
    {
        $self = new WhereCompositeCondition();
        $self->setOperand($operand);
        foreach($conditions as $field => $value) {
            if ($value instanceof WhereInterface) {
                $self->compositeConditions[] = $value;
            }
            else{
                //Default
                $condition = is_array($value) ? WhereCondition::in($field, $value) : WhereCondition::eq($field, $value);
                $self->compositeConditions[] = $condition;
            }
        }
        return $self;        
    }

    /**
     * @param array $conditions
     * @return static
     */
    public static function andX(array $conditions = []): static
    {
        return self::compose($conditions, "AND");        
    }

    /**
     * @param array $conditions
     * @return static
     */
    public static function orX(array $conditions = []): static
    {
        return self::compose($conditions, "OR");
    }
    
    /**
     * @return  string
     */ 
    public function getOperand(): string
    {
        return $this->operand;
    }

    /**
     * @param  string  $operand
     * @return  self
     */ 
    public function setOperand(string $operand): static
    {
        $this->operand = $operand;
        return $this;
    }

    /**
     * @return array
     */ 
    public function getCompositeConditions(): array
    {
        return $this->compositeConditions;
    }

    /**
     * @return array
     */
    public function getWhereConditions(): array
    {
        $flattened = array();
        foreach($this->compositeConditions as $value) {
            if ($value instanceof self) {
                $collections = $value->getWhereConditions();
                foreach($collections as $val){
                    $flattened[] = $val;
                }
            } 
            else {
                $flattened[] = $value;
            }
        }
        return $flattened;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return "";
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        return $this->getCompositeConditions();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return " (".implode($this->operand, $this->compositeConditions).") ";
    }
}