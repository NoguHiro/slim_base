<?php

class Con
{
    const CONFIG_FILE = '.env.setup.json';
    private static $config = null;

    private static function _load()
    {
        if (self::is_loaded()) {
            return ;
        }
        if (!file_exists(dirname(__DIR__) . '/' . self::CONFIG_FILE)) {
            throw new Exception('has not config file.');
        }
        $con = file_get_contents(dirname(__DIR__) . '/' . self::CONFIG_FILE);
        if (!self::$config = json_decode($con, false)) {
            throw new Exception('invalid config.');
        }
    }

    public static function url($str = null)
    {
        $domain = $_SERVER["SERVER_NAME"];
        $uri    = self::get('app_url');
        $str = !empty($str) && is_string($str) ? $str : '';
        return $domain . $uri . $str;
    }

    public static function image($name)
    {
        $base_url = self::get('app_url');
        return $base_url . '/assets/images/' . $name;
    }

    private static function is_loaded()
    {
        return !empty(self::$config);
    }

    public static function get($key = null)
    {
        self::_load();
        $obj = self::$config;
        if (is_null($key)) return $obj;
        if (!is_string($key)) return null;
        $lists = explode('.', $key);
        foreach ($lists as $list) {
            if (!isset($obj->{$list})) {
                return null;
            }
            $obj = $obj->{$list};
        }
        return $obj;
    }
} 