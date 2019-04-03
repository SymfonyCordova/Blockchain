<?php
/**
 * Created by PhpStorm.
 * User: symfony
 * Date: 2019/3/31
 * Time: 14:00
 */

namespace AppBundle\Common;


class ArrayToolkit
{

    public static function index(array $array, $name)
    {
        $indexedArray = array();

        if (empty($array)) {
            return $indexedArray;
        }

        foreach ($array as $item) {
            if (isset($item[$name])) {
                $indexedArray[$item[$name]] = $item;
                continue;
            }
        }

        return $indexedArray;
    }

    /**
     * 检查数组是否包含必须存在key,如果存在返回true 不存在返回false
     * @param array $array
     * @param array $keys
     * @param bool $strictMode
     * @return bool
     */
    public static function requires(array $array, array $keys, $strictMode = false)
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return false;
            }
            if ($strictMode && (is_null($array[$key]) || $array[$key] === '' || $array[$key] === 0)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 将不在keys里面数组array元素的去除掉,只保留存在的数组元素
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function parts(array $array, array $keys)
    {
        foreach (array_keys($array) as $key) {
            if (!in_array($key, $keys)) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}