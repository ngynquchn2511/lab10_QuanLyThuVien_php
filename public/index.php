<?php
// Bật hiển thị lỗi trong development (TẮT khi production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Không hiển thị lỗi cho user
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Bắt đầu session
session_start();

// Autoload các file cần thiết
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/Controller.php';

// Khởi tạo router và xử lý request
$router = new Router();
$router->dispatch();