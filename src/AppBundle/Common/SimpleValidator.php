<?php
/**
 * Created by PhpStorm.
 * User: symfony
 * Date: 2019/3/31
 * Time: 16:55
 */

namespace AppBundle\Common;


class SimpleValidator
{

    public static function email($value)
    {
        $value = (string) $value;
        $valid = filter_var($value, FILTER_VALIDATE_EMAIL);

        return $valid !== false;
    }

    public static function mobile($value)
    {
        return (bool) preg_match('/^1\d{10}$/', $value);
    }
}