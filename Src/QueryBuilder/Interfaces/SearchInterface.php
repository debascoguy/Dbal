<?php

namespace Emma\Dbal\QueryBuilder\Interfaces;

interface SearchInterface extends WhereInterface
{
    /**
     * @return string
     */
    public function getPrepend();

    /**
     * @return string
     */
    public function getAppend();
}
