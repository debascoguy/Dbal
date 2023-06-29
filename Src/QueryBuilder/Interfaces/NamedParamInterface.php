<?php

namespace Emma\Dbal\QueryBuilder\Interfaces;

interface NamedParamInterface
{
    /**
     * @return string
     */
    public function getParamName();
    
}
