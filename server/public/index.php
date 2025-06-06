<?php

// Simple PHP REST API for Shinkansen Challenge
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Handle preflight requests
if ($request_method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Parse the request URI
$path = parse_url($request_uri, PHP_URL_PATH);

// Route the request
switch ($path) {
    case '/':
        handleWelcome();
        break;
    case '/health':
        handleHealth();
        break;
    case '/api/trains':
        handleTrains();
        break;
    default:
        handleNotFound();
        break;
}

function handleWelcome() {
    $response = [
        'message' => 'Welcome to Shinkansen Challenge API',
        'version' => '1.0.0',
        'timestamp' => date('c')
    ];
    echo json_encode($response);
}

function handleHealth() {
    $response = [
        'status' => 'healthy',
        'timestamp' => date('c'),
        'uptime' => time()
    ];
    echo json_encode($response);
}

function handleTrains() {
    $trains = [
        [
            'id' => 1,
            'name' => 'のぞみ',
            'type' => 'Nozomi',
            'max_speed' => 320
        ],
        [
            'id' => 2,
            'name' => 'ひかり',
            'type' => 'Hikari',
            'max_speed' => 285
        ],
        [
            'id' => 3,
            'name' => 'こだま',
            'type' => 'Kodama',
            'max_speed' => 285
        ]
    ];
    echo json_encode($trains);
}

function handleNotFound() {
    http_response_code(404);
    $response = [
        'error' => 'Not Found',
        'message' => 'The requested endpoint does not exist',
        'timestamp' => date('c')
    ];
    echo json_encode($response);
}