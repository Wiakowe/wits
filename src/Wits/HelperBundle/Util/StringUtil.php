<?php

namespace Wits\HelperBundle\Util;
/**
 * Created by JetBrains PhpStorm.
 * User: patxi1980
 * Date: 28/06/12
 * Time: 15:19
 * To change this template use File | Settings | File Templates.
 */
class StringUtil
{

    /** @var array */
	protected static $special_characters = array(
        'Ä'		=> 'AE',
        'ä'		=> 'ae',
        'Á'		=> 'A',
        'À'		=> 'A',
        'á'		=> 'a',
        'à'		=> 'a',
        'Â'		=> 'A',
        'â'		=> 'a',
        'É'		=> 'E',
        'È'		=> 'E',
        'é'		=> 'e',
        'è'		=> 'e',
        'Ë'		=> 'E',
        'ë'		=> 'e',
        'Ê'		=> 'E',
        'ê'		=> 'e',
        'Í'     => 'I',
        'Ì'     => 'I',
        'Ï'     => 'I',
        'Î'     => 'I',
        'í'     => 'i',
        'ì'     => 'i',
        'ï'     => 'i',
        'î'     => 'i',
        'Ö'		=> 'OE',
        'ö'		=> 'oe',
        'Ó'		=> 'O',
        'Ò'		=> 'O',
        'ó'		=> 'o',
        'ò'		=> 'o',
        'Ô'		=> 'O',
        'ô'		=> 'o',
        'Ü'		=> 'Ue',
        'ü'		=> 'ue',
        'Ú'		=> 'U',
        'Ù'		=> 'U',
        'ú'		=> 'u',
        'ù'		=> 'u',
        'Û'		=> 'U',
        'û'		=> 'u',
        'Ñ'		=> 'N',
        'ñ'		=> 'n',
        'Ç'     => 'c',
        'ç'     => 'c',
    );

	/**
     * Takes a string and returns a slugified version of it. Slugs only consists of characters, numbers and the dash. They can be used in URLs.
     * @param  string $string String
     * @return string         Slug
     */
	public static function slugify($string)
    {
        // Convert special characters like umlauts in an ASCII version.
        $string = str_replace(
            array_keys(StringUtil::$special_characters),
            array_values(StringUtil::$special_characters),
            $string
        );

        // Lower case.
        $string = strtolower($string);

        // Remove everything that is not a lower case character, number or dash.
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);

        // Remove duplicate dashes
        $string = preg_replace('/-+/', '-', $string);

        // Remove dashes from the begining and the end.
        $string = preg_replace('/(^-)|(-$)/', '', $string);

        return $string;
    }

}
