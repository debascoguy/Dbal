<?php
/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */


namespace Emma\Dbal\QueryBuilder\Constants;


class FetchMode
{
    /**
     * Specifies that the fetch method shall return each row as a key-value pair array indexed
     * by first column and the value is from the second column as returned in the corresponding result set.
     * @link https://phpdelusions.net/pdo/fetch_modes 
     */
    const FETCH_KEY_PAIR = \PDO::FETCH_KEY_PAIR;  

    /**
     * Specifies that the fetch method shall return each row as an array indexed
     * by first column as returned in the corresponding result set. If the result
     * set contains multiple columns with the same name,
     * <b>PDO::FETCH_UNIQUE</b> returns
     * only a single value per column name.
     * @link https://phpdelusions.net/pdo/fetch_modes
     */
    const FETCH_UNIQUE = \PDO::FETCH_UNIQUE;
    
    /**
     * This mode groups the returned rows into a nested array, 
     * where indexes will be unique values from the first column, 
     * and values will be arrays similar to ones returned by regular fetchAll()
     * @link https://phpdelusions.net/pdo/fetch_modes
     */
    const FETCH_GROUP = \PDO::FETCH_GROUP;
    
    /**
     * Specifies that the fetch method shall return each row as an object with
     * variable names that correspond to the column names returned in the result
     * set. <b>PDO::FETCH_LAZY</b> creates the object variable names as they are accessed.
     * Not valid inside <b>PDOStatement::fetchAll</b>.
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_LAZY = \PDO::FETCH_LAZY;

    /**
     * Specifies that the fetch method shall return each row as an array indexed
     * by column name as returned in the corresponding result set. If the result
     * set contains multiple columns with the same name,
     * <b>PDO::FETCH_ASSOC</b> returns
     * only a single value per column name.
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_ASSOC = \PDO::FETCH_ASSOC;

    /**
     * returns an array with the same form as PDO::FETCH_ASSOC, 
     * except that if there are multiple columns with the same name, the value referred to by that key will be 
     * an array of all the values in the row that had that column name
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_NAMED = \PDO::FETCH_NAMED;

    /**
     * Specifies that the fetch method shall return each row as an array indexed
     * by column number as returned in the corresponding result set, starting at
     * column 0.
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_NUM = \PDO::FETCH_NUM;

    /**
     * Specifies that the fetch method shall return each row as an array indexed
     * by both column name and number as returned in the corresponding result set,
     * starting at column 0.
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_BOTH = \PDO::FETCH_BOTH;

    /**
     * Specifies that the fetch method shall return each row as an object with
     * property names that correspond to the column names returned in the result
     * set.
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_OBJ = \PDO::FETCH_OBJ;

    /**
     * Specifies that the fetch method shall return TRUE and assign the values of
     * the columns in the result set to the PHP variables to which they were
     * bound with the <b>PDOStatement::bindParam</b> or
     * <b>PDOStatement::bindColumn</b> methods.
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_BOUND = \PDO::FETCH_BOUND;

    /**
     * Specifies that the fetch method shall return only a single requested
     * column from the next row in the result set.
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_COLUMN = \PDO::FETCH_COLUMN;

    /**
     * Specifies that the fetch method shall return a new instance of the
     * requested class, mapping the columns to named properties in the class.
     * The magic
     * <b>__set</b>
     * method is called if the property doesn't exist in the requested class
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_CLASS = \PDO::FETCH_CLASS;

    /**
     * Determine the class name from the value of first column.
     * Example: $pdo->query("SELECT 'User', name FROM users")
     * Result: object(User)#3 (1) { ["name"]=> string(4) "John" }
     * @link https://www.php.net/manual/en/pdo.constants.php#pdo.constants.fetch-classtype
     */
    const FETCH_CLASSTYPE = \PDO::FETCH_CLASSTYPE;

    /**
     * Specifies that the fetch method shall update an existing instance of the
     * requested class, mapping the columns to named properties in the class.
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_INTO = \PDO::FETCH_INTO;

    /**
     * Allows completely customize the way data is treated on the fly (only
     * valid inside <b>PDOStatement::fetchAll</b>).
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_FUNC = \PDO::FETCH_FUNC;

    /**
     * when used with PDO::FETCH_CLASS, the constructor of the class is called 
     * before the properties are assigned from the respective column values.
     * @link https://www.php.net/manual/en/pdostatement.fetch.php
     */
    const FETCH_PROPS_LATE = \PDO::FETCH_PROPS_LATE;

    /**
     * For a PDOStatement object representing a scrollable cursor for which the cursor_orientation parameter is set to
     * PDO::FETCH_ORI_ABS, this value specifies the absolute number of the row in the result set that shall be fetched.
     * @link https://www.php.net/manual/en/pdostatement.fetch.php
     */
    const FETCH_ORI_ABS = \PDO::FETCH_ORI_ABS;

    /**
     * For a PDOStatement object representing a scrollable cursor for which the cursor_orientation parameter is set to
     * PDO::FETCH_ORI_REL, this value specifies the row to fetch relative to the cursor position before PDOStatement::fetch() was called.
     * @link https://www.php.net/manual/en/pdostatement.fetch.php
     */
    const FETCH_ORI_REL = \PDO::FETCH_ORI_REL;

}