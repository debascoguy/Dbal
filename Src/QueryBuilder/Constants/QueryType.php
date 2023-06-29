<?php
/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */


namespace Emma\Dbal\QueryBuilder\Constants;


class QueryType
{
    const UNSPECIFIED_STATEMENT = -1;

    const INSERT_STATEMENT = 1;

    const UPDATE_STATEMENT = 2;

    const DELETE_STATEMENT = 3;

    const SELECT_STATEMENT = 4;

    const SELECT_ONE_STATEMENT = 5;

}