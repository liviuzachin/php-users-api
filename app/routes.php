<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$method = $_SERVER['REQUEST_METHOD'];

$userController = new App\Controllers\UserController();

header("Content-type: application/json; charset=utf-8");

if ($uri[1] === 'api' && isset($uri[4]) && $uri[4] !== '') { # /api/users/{id}/etc.
    header("HTTP/1.1 404 Not Found");
    echo json_encode([
        'error' => 'Not Found.',
    ]);
} else if ($uri[1] === 'api' && isset($uri[2]) && $uri[2] === 'users') {
    switch ($method) {
        case 'POST': # /api/users
            $data = json_decode(file_get_contents('php://input'), true);
            $userController->store($data);
            break;
        case 'GET': 
            if (isset($uri[3])) { # /api/users/{id}
                $userController->show($uri[3]);
            } else { # /api/users
                $userController->index();
            }
            break;
        case 'PUT': # /api/users/{id}
            if (isset($uri[3])) {
                $data = json_decode(file_get_contents('php://input'), true);
                $userController->update($uri[3], $data);
            }
            break;
        case 'DELETE': # /api/users/{id}
            if (isset($uri[3])) {
                $userController->destroy($uri[3]);
            }
            break;
        default:
            header("HTTP/1.1 405 Method Not Allowed");
            break;
    }
} else if ($uri[1] === '') {
    echo json_encode([
        'app' => [
            'name' => 'users-rest-api',
            'version' => 1,
        ],
        'urls' => [
            'users' => '/api/users',
            'user' => '/api/users/{id}',
        ]
    ]);
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode([
        'error' => 'Not Found.',
    ]);
}
