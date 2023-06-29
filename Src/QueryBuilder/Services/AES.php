<?php
namespace Emma\Dbal\QueryBuilder\Services;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class AES
{
    public const SHA_0 = 0; //equivalent to SHA_256
    public const SHA_256 = 256; //equivalent to SHA_0
    public const SHA_224 = 224;
    public const SHA_384 = 384;
    public const SHA_512 = 512;

    /**
     * @param string $fieldName
     * @param $key
     * @param int $sha2KeySize [0 is equivalent to 256]
     * @return string
     */
    public static function encryptField(string $fieldName, $key, int $sha2KeySize = 0)
    {
        return " AES_ENCRYPT($fieldName, UNHEX(SHA2('$key', $sha2KeySize))) ";
    }

    /**
     * @param string $fieldName
     * @param $key
     * @param int $sha2KeySize [0 is equivalent to 256]
     * @return string
     */
    public static function decryptField(string $fieldName, $key, int $sha2KeySize = 0)
    {
        return " AES_DECRYPT($fieldName, UNHEX(SHA2('$key', $sha2KeySize)))) ";
    }

}