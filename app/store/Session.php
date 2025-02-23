<?php

namespace App\Store;

class Session
{

    public function __construct()
    {   
        // start the session if it hasn't been started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Store a value in the session
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    // Retrieve a value from the session
    public function get($key, $default = null)
    {
        return $this->has($key) ? $_SESSION[$key] : $default;
    }

    // Check if a key exists in the session
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    // Remove a value from the session
    public function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    // Clear the session
    public function clear()
    {
        session_unset();
    }

    // Destroy the session
    public function destroy()
    {
        session_destroy();
    }
}
