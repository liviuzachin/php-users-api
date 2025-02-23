<?php

namespace App\Models;

use App\Store\UserStore;

class User
{
    public $id;
    public $firstName;
    public $lastName;
    public $email;
    public $dateOfBirth;

    private static $store = null;

    public function __construct($data = [])
    {
        $this->id = $data['id'];
        $this->firstName = $data['firstName'];
        $this->lastName = $data['lastName'];
        $this->email = $data['email'];
        $this->dateOfBirth = $data['dateOfBirth'];
    }

    public static function store() {
        if (self::$store === null) {
            self::$store = UserStore::instance();
        }
        return self::$store;
    }

    public static function ids()
    {
        $users = self::store()->all();
        return array_column($users, 'id');
    }

    public static function lastId() 
    {
        $userIds = self::ids();
        return end($userIds) ?? 0;
    }

    public static function nextId() 
    {
        return self::lastId() + 1;
    }

    public static function create(array $data)
    {
        $userId = self::nextId();
        $data['id'] = $userId;
        self::store()->createOrUpdate($data);
        return new User($data);
    }

    public static function all($asArray = false)
    {
        return array_map(function ($user) use ($asArray) {
            $user = new User($user);
            if ($asArray) {
                return $user->toArray();
            }
            return $user;
        }, self::store()->all());
    }

    public static function find(int $id)
    {
        $userData = self::store()->find($id);
        return $userData ? new User($userData) : null;
    }

    public static function update(array $data)
    {
        self::store()->createOrUpdate($data);
        return new User($data);
    }

    public static function destroy(int $id)
    {
        self::store()->destroy($id);
        return $id;
    }

    // Check if an email address exists among the users
    public static function emailExists(string $email)
    {
        $users = self::all();
        $emails = array_column($users, 'email');
        $foundEmailIdx = array_search($email, $emails);
        return $foundEmailIdx !== false;
    }

    // Validate the user's age (18+)
    public static function isAgeValid(string $dateOfBirth)
    {
        return self::calculateAge($dateOfBirth) >= 18;
    }

    // Calculate age based on dateOfBirth
    public static function calculateAge(string $dateOfBirth)
    {
        $dob = new \DateTime($dateOfBirth);
        $today = new \DateTime();
        return $today->diff($dob)->y;
    }

    // Convert user data to an array with age included
    public function toArray($exclude = [])
    {
        $data = [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'dateOfBirth' => $this->dateOfBirth,
            'age' => self::calculateAge($this->dateOfBirth),
        ];

        foreach ($exclude as $prop) {
            unset($data[$prop]);
        }

        return $data;
    }
}
