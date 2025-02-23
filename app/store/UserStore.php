<?php

namespace App\Store;

class UserStore
{
    private $session;

    private static $instance = null;

    public function __construct()
    {
        $this->session = new Session();
        // Ensure the 'users' session array exists
        if (!$this->session->has('users')) {
            $this->session->set('users', []);
        }
    }

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function createOrUpdate($user)
    {
        $users = $this->session->get('users');
        $users[$user['id']] = $user;
        $this->session->set('users', $users);
    }

    public function find($userId)
    {
        $users = $this->session->get('users');
        return $users[$userId] ?? null;
    }

    public function all()
    {
        return array_values($this->session->get('users'));
    }

    public function destroy($userId)
    {
        $users = $this->session->get('users');
        if (isset($users[$userId])) {
            unset($users[$userId]);
            $this->session->set('users', $users);
        }
    }

    public function destroyAll()
    {
        $this->session->set('users', []);
    }
}
