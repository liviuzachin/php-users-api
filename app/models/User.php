<?php

namespace App\Models;

use App\Store\Session;

class User
{
    public $id;
    public $firstName;
    public $lastName;
    public $email;
    public $dateOfBirth;

    public function __construct($data = [])
    {
        $this->id = $data['id'];
        $this->firstName = $data['firstName'];
        $this->lastName = $data['lastName'];
        $this->email = $data['email'];
        $this->dateOfBirth = $data['dateOfBirth'];
    }

    public static function getData()
    {
        $session = Session::instance();
        return $session->get('users', []);
    }

    public static function setData(array $data)
    {
        $session = Session::instance();
        return $session->set('users', $data);
    }

    public static function ids()
    {
        $users = self::getData();
        return array_column($users, 'id');
    }

    public static function indexById(int $id) {
        return array_search($id, self::ids());
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

    // Create user
    public static function create(array $data)
    {
        $userId = self::nextId();
        $data['id'] = $userId;
        $usersData = self::getData();
        $usersData[] = $data;
        self::setData($usersData);
        return self::find($userId);
    }

    // Get all users
    public static function all($asArray = false)
    {
        $usersData = self::getData();
        return array_map(function ($user) use ($asArray) {
            $user = new User($user);
            if ($asArray) {
                return $user->toArray();
            }
            return $user;
        }, $usersData);
    }

    // Find one user by ID
    public static function find(int $id)
    {
        $usersData = self::getData();
        $userData = array_find($usersData, function ($user) use ($id) {
            return $user['id'] === intval($id);
        });
        return $userData ? new User($userData) : null;
    }

    // Update user
    public static function update(array $data)
    {
        $usersData = self::getData();
        $userIdx = self::indexById($data['id']);
        $usersData[$userIdx] = $data;
        self::setData($usersData);
        return new User($data);
    }

    // Delete user
    public static function destroy(int $id)
    {
        $usersData = self::getData();
        $userIdx = self::indexById($id);
        array_splice($usersData, $userIdx, 1);
        self::setData($usersData);
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
        $age = self::calculateAge($dateOfBirth);
        return $age >= 18;
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
