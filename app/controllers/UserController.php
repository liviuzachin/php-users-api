<?php

namespace App\Controllers;

use App\Models\User;

class UserController
{
    public function store($data)
    {
        if (!$this->isValidUser($data)) {
            return;
        }
        $user = User::create($data);
        header('HTTP/1.1 201 Created');
        echo json_encode($user->toArray());
    }

    public function index()
    {
        $users = User::all(true);
        echo json_encode($users);
    }

    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            echo json_encode($user->toArray());
        } else {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'User not found.']);
        }
    }

    public function update($id, $data)
    {
        $user = User::find($id);
        if (!$user) {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'User not found.']);
            return;
        }

        $data = array_merge($user->toArray(['age']), $data);

        if (!$this->isValidUser($data)) {
            return;
        }

        $updatedUser = User::update($data);
        echo json_encode($updatedUser->toArray());
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            User::destroy($id);
            header('HTTP/1.1 204 No Content');
        } else {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'User not found.']);
        }
    }

    public function isValidUser($data = [])
    {
        $userId = $data['id'] ?? null;
        $email = $data['email'] ?? null;
        $dateOfBirth = $data['dateOfBirth'] ?? null;

        // required fields
        foreach (['firstName', 'email', 'dateOfBirth'] as $field) {
            if (empty($data[$field])) {
                $this->showError("The $field field is required.");
                return false;
            }
            if (!is_string($data[$field])) {
                $this->showError("The $field must be a string.");
                return false;
            }
        }

        // fields limited to 128 characters
        foreach (['firstName', 'lastName'] as $field) {
            if (isset($data[$field]) && strlen($data[$field]) > 128) {
                $this->showError("The $field field should not be greater than 128 characters.");
                return false;
            }
        }

        // email should be valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->showError('Invalid email format.');
            return false;
        }

        // date of birth must be a valid date
        $datetimeOfBirth = \DateTime::createFromFormat('Y-m-d', $dateOfBirth);
        if (!($datetimeOfBirth && $datetimeOfBirth->format('Y-m-d') === $dateOfBirth)) {
            $this->showError('Date of birth must be a valid date.');
            return false;
        }

        // user should be 18+
        if (!User::isAgeValid($dateOfBirth)) {
            $this->showError('User must be 18 years or older.');
            return false;
        }

        // email should be unique among users
        if (User::emailExists($email)) {
            // check if email belongs to the user
            if ($userId) {
                $user = User::find($userId);
                if ($user && $user->email === $email) {
                    return true;
                }
            }

            $this->showError('A user with this email address already exists.');
            return false;
        }

        return true;
    }

    public function showError(string $error) {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => $error]);
    }
}
