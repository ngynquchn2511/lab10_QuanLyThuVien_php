<?php
class Router {
    public function dispatch() {
        // Lấy controller và action từ query string
        $controller = $_GET['c'] ?? 'books';
        $action = $_GET['a'] ?? 'index';
        
        // Whitelist controller (bảo mật)
        $allowedControllers = ['books', 'borrowers', 'borrows'];
        if (!in_array($controller, $allowedControllers)) {
            $controller = 'books';
        }
        
        // Tên class controller
        $controllerClass = ucfirst($controller) . 'Controller';
        $controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';
        
        if (!file_exists($controllerFile)) {
            die("Controller không tồn tại");
        }
        
        require_once $controllerFile;
        $controllerObj = new $controllerClass();
        
        // Kiểm tra method tồn tại
        if (!method_exists($controllerObj, $action)) {
            die("Action không tồn tại");
        }
        
        // Gọi action
        $controllerObj->$action();
    }
}