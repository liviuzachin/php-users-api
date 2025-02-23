<?php

namespace Tests;

use App\Controllers\UserController;
use App\Models\User;
use App\Store\UserStore;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{

    protected $userController;

    protected static $userStore;

    public static function setUpBeforeClass(): void
    {
        // get the user store instance
        self::$userStore = UserStore::instance();
    }

    protected function setUp(): void
    {
        // Clear the users data before each test
        self::$userStore->destroyAll();

        // Instantiate the UserController
        $this->userController = new UserController();
    }

    public function testCreateUserValid()
    {
        $data = [
            'firstName' => 'Dwight',
            'lastName' => 'Schrute',
            'email' => 'dwight@dmpc.com',
            'dateOfBirth' => '1980-01-01',
        ];

        ob_start();
        $this->userController->store($data);
        $output = ob_get_clean();

        // Check if the user was created and the output has an ID
        $user = json_decode($output, true);
        $this->assertEquals('Dwight', $user['firstName']);
        $this->assertEquals('Schrute', $user['lastName']);
        $this->assertEquals('dwight@dmpc.com', $user['email']);
        $this->assertEquals('1980-01-01', $user['dateOfBirth']);
        $this->assertEquals(45, $user['age']); // Should be 45 based on the birthdate

        // Ensure the user was stored
        $userInStore = self::$userStore->find($user['id']);
        $this->assertNotNull($userInStore);
    }

    public function testCreateUserInvalid()
    {
        $data = [
            'lastName' => 'Bernard',
            'email' => 'andy@dmpc.com',
            'dateOfBirth' => '1982-01-01',
        ];
        ob_start();
        $this->userController->store($data);
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertEquals('The firstName field is required.', $response['error']);
    }

    public function testCreateUserWithInvalidLastName()
    {
        $data = [
            'firstName' => 'Andy',
            'lastName' => 'Andrew Baines Bernard is introduced in season 3 as the Regional Director in Charge of Sales at the Stamford branch of paper distribution company',
            'email' => 'andy@dmpc.com',
            'dateOfBirth' => '1982-01-01',
        ];
        ob_start();
        $this->userController->store($data);
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertEquals('The lastName field should not be greater than 128 characters.', $response['error']);
    }

    public function testCreateUserWithInvalidEmail()
    {
        $data = [
            'firstName' => 'Michael',
            'lastName' => 'Scott',
            'email' => 'mscott#dmpc.com',
            'dateOfBirth' => '2000-01-01'
        ];

        ob_start();
        $this->userController->store($data);
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertEquals('Invalid email format.', $response['error']);
    }

    public function testCreateUserUnderage()
    {
        $data = [
            'firstName' => 'Junior',
            'lastName' => 'Scott',
            'email' => 'jscott@dmpc.com',
            'dateOfBirth' => '2010-01-01'
        ];

        ob_start();
        $this->userController->store($data);
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertEquals('User must be 18 years or older.', $response['error']);
    }

    public function testGetAllUsers()
    {
        $data = [
            'firstName' => 'Dwight',
            'lastName' => 'Schrute',
            'email' => 'dschrute@dmpc.com',
            'dateOfBirth' => '1990-05-15'
        ];
        ob_start();
        $this->userController->store($data); // Create a user
        ob_get_clean();

        ob_start();
        $this->userController->index();
        $output = ob_get_clean();

        $users = json_decode($output, true);

        $this->assertCount(1, $users);
        $this->assertEquals('Dwight', $users[0]['firstName']);
        $this->assertEquals(34, $users[0]['age']); // User age based on date of birth
    }

    public function testGetUserById()
    {
        $data = [
            'firstName' => 'Pam',
            'lastName' => 'Beesly',
            'email' => 'pam@dmpc.com',
            'dateOfBirth' => '1995-06-20'
        ];
        $createdUser = User::create($data); // Create a user

        ob_start();
        $this->userController->show($createdUser->id);
        $output = ob_get_clean();

        $user = json_decode($output, true);
        $this->assertEquals('Pam', $user['firstName']);
        $this->assertEquals(29, $user['age']);
    }

    public function testUpdateUser()
    {
        // Create a user first
        $data = [
            'firstName' => 'John',
            'lastName' => 'Krasinski',
            'email' => 'john@dmpc.com',
            'dateOfBirth' => '1992-10-04'
        ];
        $createdUser = User::create($data);

        // Now, update the user
        $updateData = [
            'firstName' => 'Jim',
            'lastName' => 'Halpert',
            'email' => 'jim@dmpc.com',
        ];
        ob_start();
        $this->userController->update($createdUser->id, $updateData);
        $output = ob_get_clean();

        $updatedUser = json_decode($output, true);
        $this->assertEquals('Jim', $updatedUser['firstName']);
        $this->assertEquals('Halpert', $updatedUser['lastName']);
        $this->assertEquals('jim@dmpc.com', $updatedUser['email']);
    }

    public function testDeleteUser()
    {
        $data = [
            'firstName' => 'Kevin',
            'lastName' => 'Malone',
            'email' => 'kevin@dmpc.com',
            'dateOfBirth' => '1989-12-25'
        ];
        $createdUser = User::create($data);

        ob_start();
        $this->userController->destroy($createdUser->id);
        ob_get_clean();

        // Check if the user is deleted
        $user = User::find($createdUser->id);
        $this->assertNull($user);
    }

    public function testGetUserNotFound()
    {
        ob_start();
        $this->userController->show(99999); // Non-existent ID
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertEquals('User not found.', $response['error']);
    }

    public function testDeleteUserNotFound()
    {
        ob_start();
        $this->userController->destroy(99999); // Non-existent ID
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertEquals('User not found.', $response['error']);
    }
}
