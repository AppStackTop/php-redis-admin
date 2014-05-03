<?php

final class Session
{
    protected static $_instance = Null;

    protected function __construct()
    {
        if (PHP_SAPI != 'cli') {
        ini_set('session.gc_probability', App::instance()->config['session']['gc_probability']);
        ini_set('session.gc_divisor', 100);
        ini_set('session.gc_maxlifetime', App::instance()->config['session']['lifetime']);

        session_name(App::instance()->config['session']['name']);
        session_start();
        }
    }

    public static function instance()
    {
        if (!isset(self::$_instance))
            self::$_instance = new self;

        return self::$_instance;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;

        if (PHP_SAPI != 'cli')
            session_write_close();
    }

    public function get($key, $default = Null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public function get_once($key, $default = Null)
    {
        $result = $this->get($key, $default);

        unset($_SESSION[$key]);

        return $result;
    }

    public function del($key)
    {
        unset($_SESSION[$key]);
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function __get($key)
    {
        return $this->get($key);
    }
}
