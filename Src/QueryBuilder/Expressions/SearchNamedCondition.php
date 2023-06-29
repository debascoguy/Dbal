<?php
namespace Emma\Dbal\QueryBuilder\Expressions;

use Emma\Dbal\QueryBuilder\Interfaces\NamedParamInterface;
use Emma\Dbal\QueryBuilder\Services\ParamCounter;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class SearchNamedCondition extends SearchCondition implements NamedParamInterface
{
    /**
     * @param $field
     * @param $operand
     * @param $value
     */
    public function __construct($field, $operand, $value, $append = "%", $prepend = "%")
    {
        parent::__construct($field, $operand, $value, $append, $prepend);
        $this->setParamName(":".$field.ParamCounter::next());
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $append
     * @param string $prepend
     * @return SearchNamedCondition
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
     * @return SearchNamedCondition
     */
    public static function notLike(string $field, mixed $value, string $append = "%", string $prepend = "%"): static
    {
        return new self($field, "NOT LIKE", $value, $append, $prepend);
    }
    
}