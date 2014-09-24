<?php

class Session
{
    private static $loaded = false;

    public static function load()
    {
        if (!self::$loaded) session_start();
    }

    public static function get($key = null)
    {
        if (empty($key)) return $_SESSION;
        if (is_array($key)) {
            $ret = array();
            foreach($key as $k) {
                if (isset($_SESSION[$k])) $ret[$k] = $_SESSION[$k];
            }
            return $ret;
        }
        return self::is_valid_key($key) ? self::has($key) ? $_SESSION[$key]: null: null;
    }

    private static function is_valid_key($key)
    {
        return is_string($key) || is_int($key);
    }

    private static function has($key) {
        return isset($_SESSION[$key]);
    }

    public static function set($key, $data)
    {
        if (self::is_valid_key($key)) {
            $_SESSION[$key] = $data;
            return true;
        }
        return false;
    }

    public static function remove($key)
    {
        if (self::has($key)) {
            unset($_SESSION[$key]);
            return true;
        }
        return false;
    }

    public static function close()
    {
        if (self::$loaded) session_destroy();
    }
}