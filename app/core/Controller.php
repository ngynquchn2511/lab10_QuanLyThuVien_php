<?php
class Controller {
    protected function view($viewPath, $data = []) {
        extract($data);
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/' . $viewPath . '.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }
    
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    protected function setFlash($key, $message) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][$key] = $message;
    }
    
    protected function getFlash($key) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
}