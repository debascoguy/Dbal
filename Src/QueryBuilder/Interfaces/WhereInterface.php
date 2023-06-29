<?php

namespace Emma\Dbal\QueryBuilder\Interfaces;

interface WhereInterface
{
    /**
     * @return string
     */
    public function getField();

    /**
     * @return string
     */
    public function getOperand();

    /**
     * @return array|int|string
     */
    public function getValue();

    /**
     * @return string
     */
    public function __toString();
}
