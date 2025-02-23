# PHP User API Project

This project is a simple RESTful API built with PHP that provides CRUD functionality for managing users. It supports the following operations:

- **POST** `/api/users` - Create a new user
- **GET** `/api/users` - Retrieve all users
- **GET** `/api/users/{id}` - Retrieve a single user by ID
- **PUT** `/api/users/{id}` - Update an existing user by ID
- **DELETE** `/api/users/{id}` - Delete a user by ID

## Requirements

- PHP 8.4 or higher
- Composer (for dependency management)
- PHPUnit (for testing)

## Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/liviuzachin/php-users-api.git
cd php-users-api
```

### Step 2: Install Dependencies

Use Composer to install all required dependencies.

```bash
composer install
```

### Step 3: Run the PHP Built-in Server (for Local Development)

To run the API locally, you can use PHP's built-in server:

```bash
composer serve
```

This will serve your project on `http://localhost:8000`.

### Step 4: Run Tests

This project uses PHPUnit for testing. To run the tests, use the following command:

```bash
composer test
```

This will run the tests defined in the `/tests` directory using the configuration from `phpunit.xml`.

---

## API Endpoints

### **POST /api/users**

Create a new user.

**Request Body**:
```json
{
    "firstName": "Michael",
    "lastName": "Scott",
    "email": "michael@dmpc.com",
    "dateOfBirth": "1980-01-01"
}
```

**Response**:
- 201 Created: User successfully created, returning the newly created user object.
- 400 Bad Request: Invalid data (e.g., underage user, invalid email, etc.).

### **GET /api/users**

Retrieve all users.

**Response**:
- 200 OK: Returns an array of users with their `firstName`, `lastName`, `email`, `dateOfBirth`, and `age`.

Example response:
```json
[
    {
        "id": 1,
        "firstName": "Jim",
        "lastName": "Halpert",
        "email": "jim@dmpc.com",
        "dateOfBirth": "1980-01-01",
        "age": 25
    }
]
```

### **GET /api/users/{id}**

Retrieve a single user by ID.

**Response**:
- 200 OK: Returns the user's details.
- 404 Not Found: User not found.

### **PUT /api/users/{id}**

Update an existing user's information.

**Request Body**:
```json
{
    "firstName": "Pam",
    "lastName": "Beesly",
    "email": "pam@dmpc.com",
    "dateOfBirth": "1992-10-04"
}
```

**Response**:
- 200 OK: User successfully updated, returning the updated user object.
- 404 Not Found: User not found.
- 400 Bad Request: Invalid data (e.g., invalid email, underage user).

### **DELETE /api/users/{id}**

Delete a user by ID.

**Response**:
- 200 OK: User successfully deleted.
- 404 Not Found: User not found.

---

## Project Structure

```
/app
    /controllers
        UserController.php      # Handles the user API logic
    /models
        User.php                # User model class
    /store
        Session.php             # PHP Session store class
    /bootstrap.php              # App bootstrap
    /routes.php                 # Routes configuration
/public
    index.php                   # Entry point for the application
/tests
    UserControllerTest.php      # PHPUnit test cases for the UserController
composer.json                   # Composer configuration and dependencies
composer.lock                   # Composer dependencies lock file
phpunit.xml                     # PHPUnit configuration
herd.yml                        # Herd configuration (optional, if using Herd)
README.md                       # This file
LICENSE                         # MIT Lincense file
```

---

## License

This project is open-source and available under the [MIT License](LICENSE).

