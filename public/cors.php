<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
    http_response_code(204);
    exit;
}
require __DIR__ . '/index.php';
