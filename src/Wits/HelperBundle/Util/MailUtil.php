<?php
namespace Wits\HelperBundle\Util;

/**
 * @author Roger Llopart Pla <lumbendil@gmail.com>
 */
class MailUtil
{
    public static function removeSignature($string)
    {
        return preg_replace('/^--\s+$.*\Z/sm', '', $string);
    }

    public static function removeReply($string)
    {
        $string = preg_replace('/^>+.*$/m', '', $string);
        $string = preg_replace('/^.*,\s*<.+@.+\..+>.+:\s+$/m', '', $string);

        return $string;
    }
}
