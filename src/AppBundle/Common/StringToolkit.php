<?php
namespace AppBundle\Common;

class StringToolkit
{

    public static function toCamelCase($str, $ucfirst = false)
    {
        $str = ucwords(str_replace('_', ' ', $str));
        $str = str_replace(' ', '', lcfirst($str));

        return $ucfirst ? ucfirst($str) : $str;
    }

    public static function toUnderScore($str)
    {
        $arr = preg_split('/(?=[A-Z])/', $str);

        return strtolower(trim(implode('_', $arr), '_'));
    }

    public static function createRandomString($length)
    {
        $start = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = null;
        for ($i = 0; $i < $length; ++$i) {
            $rand = rand(0, 61);
            $code = $code.$start[$rand];
        }

        return $code;
    }

    public static function createRandomNumber($length)
    {
        $start = '0123456789012345678901234567890123456789';
        $code = null;
        for ($i = 0; $i < $length; ++$i) {
            $rand = rand(0, 39);
            $code = $code.$start[$rand];
        }

        return $code;
    }

}