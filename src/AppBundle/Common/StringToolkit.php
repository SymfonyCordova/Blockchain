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

}