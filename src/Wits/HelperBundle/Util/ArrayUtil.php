<?php
namespace Wits\HelperBundle\Util;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class ArrayUtil
{
    public static function setElementAsKey(array $array, $element)
    {
        $result = array();

        foreach($array as $item) {
            if (!isset($item[$element])) {
                throw new \RuntimeException('The $element key is supposed to ' .
                    'be set for all the array elements.');
            }
            $value = $item[$element];

            if (!is_integer($value) && !is_string($value)) {
                throw new \RuntimeException(
                    'The value for the $element key should be a string or an ' .
                        'integer, ' . gettype($value) . ' found.'
                );
            }

            unset($item[$element]);

            $result[$value] = $item;
        }
        return $result;
    }

    public static function setElementAsKeyValue(array $array, $key, $value)
    {
        $result = array();

        foreach($array as $item){
            if (!isset($item[$key])) {
                throw new \RuntimeException('The $key key is supposed to ' .
                    'be set for all the array elements.');
            }

            $keyValue = $item[$key];

            if (!is_integer($keyValue) && !is_string($keyValue)) {
                throw new \RuntimeException(
                    'The value for the $key key should be a string or an ' .
                        'integer, ' . gettype($keyValue) . ' found.'
                );
            }

            unset($item[$key]);

            if (!isset($item[$value])) {
                throw new \RuntimeException('The $value value is supposed to ' .
                    'be set for all the array elements.');
            }

            $valueValue = $item[$value];

            unset($item[$value]);

            $result[$keyValue] = $valueValue;
        }
        return $result;
    }
}
