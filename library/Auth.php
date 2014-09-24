<?php

class Auth
{

    private static $validates = array('id', 'password');

    public static function check()
    {
        $exists = array('Session', 'Con');
        foreach ($exists as $class_name) {
            if (!class_exists($class_name, false)) {
                throw new Exception("invalid error unloaded class ${class_name}");
            }
        }
    }

    public static function login($successCallback = null, $errorCallback = null)
    {
        $errors = self::_check_login();
        if (empty($errors)) {
            return is_callable($successCallback) ? $successCallback() : true;
        }
        else {
            return is_callable($errorCallback) ? $errorCallback($errors) : false;
        }
    }

    private static function _check_login()
    {
        $errors = array();
        $admin = Con::get('admin');
        foreach (self::$validates as $key) {
            if (!isset($_POST[$key])) {
                $errors[$key] = "${key} がありません <br />";
                break;
            }
            if ($_POST[$key] !== $admin->{$key}) {
                $errors[$key] = "${key} が間違えています <br />";
                break;
            }
        }
        return $errors;
    }

    public static function logout()
    {
        Session::remove(Con::get('admin.session_name'));
    }

    public static function redirect_not_logged($app)
    {
        if (!self::is_logged()) {
            $app->redirect(Con::get('app_url') . '/login');
        }
    }

    public static function logged($successCallback = null, $errorCallback = null)
    {
        $session_secure_key = Session::get(Con::get('admin.session_name'));
        $secure_key         = Con::get('admin.secure_key');
        if ($session_secure_key == $secure_key) {
            return is_callable($successCallback) ? $successCallback() : true;
        }
        else {
            return is_callable($errorCallback) ? $errorCallback() : false;
        }
    }

    public static function is_logged()
    {
        $session_secure_key = Session::get(Con::get('admin.session_name'));
        $secure_key         = Con::get('admin.secure_key');
        return $session_secure_key == $secure_key;
    }

}