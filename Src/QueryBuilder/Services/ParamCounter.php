<?php

namespace Emma\Dbal\QueryBuilder\Services;

class ParamCounter {
    
    /**
     * @var int
     */
    private static int $counter = 0;

    public static function next(): int
    {
        if (self::$counter == null) {
            self::$counter = 0;
        }
        return self::$counter++;
    }

}